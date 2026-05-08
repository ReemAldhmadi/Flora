<?php
// =============================================
// Flora API - php/api.php
// Handles AJAX requests from the frontend
// =============================================

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    // ---- CART ----
    case 'cart_add':
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 1);
        if ($productId < 1) jsonResponse(['error' => 'Invalid product'], 400);
        $ok = addToCart($productId, $qty);
        jsonResponse(['success' => $ok, 'cart_count' => getCartCount()]);

    case 'cart_remove':
        $productId = (int)($_POST['product_id'] ?? 0);
        removeFromCart($productId);
        $cart = getCart();
        $subtotal = getCartTotal();
        $vat = $subtotal * VAT_RATE;
        jsonResponse([
            'success' => true,
            'cart_count' => getCartCount(),
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $subtotal + $vat,
            'cart_empty' => empty($cart)
        ]);

    case 'cart_update':
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 1);
        updateCartQty($productId, $qty);
        $cart = getCart();
        $subtotal = getCartTotal();
        $vat = $subtotal * VAT_RATE;
        $itemTotal = isset($cart[$productId]) ? $cart[$productId]['price'] * $cart[$productId]['quantity'] : 0;
        jsonResponse([
            'success' => true,
            'cart_count' => getCartCount(),
            'item_total' => $itemTotal,
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $subtotal + $vat,
        ]);

    case 'cart_get':
        $cart = getCart();
        $subtotal = getCartTotal();
        $vat = $subtotal * VAT_RATE;
        jsonResponse([
            'items' => array_values($cart),
            'count' => getCartCount(),
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $subtotal + $vat
        ]);

    // ---- WISHLIST ----
    case 'wishlist_toggle':
        if (!isLoggedIn()) {
            jsonResponse(['error' => 'Please login to use wishlist', 'login_required' => true], 401);
        }
        $userId = $_SESSION['user_id'];
        $productId = (int)($_POST['product_id'] ?? 0);
        $db = getDB();
        $check = $db->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
        $check->execute([$userId, $productId]);
        if ($check->fetch()) {
            $db->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?")->execute([$userId, $productId]);
            jsonResponse(['success' => true, 'wishlisted' => false]);
        } else {
            $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?,?)")->execute([$userId, $productId]);
            jsonResponse(['success' => true, 'wishlisted' => true]);
        }

    // ---- PRODUCTS ----
    case 'products_get':
        $db = getDB();
        $categorySlug = $_GET['category'] ?? 'all';
        $sort = $_GET['sort'] ?? 'featured';
        $minPrice = (float)($_GET['min_price'] ?? 0);
        $maxPrice = (float)($_GET['max_price'] ?? 9999);

        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.price BETWEEN ? AND ?";
        $params = [$minPrice, $maxPrice];

        if ($categorySlug !== 'all') {
            $sql .= " AND c.slug = ?";
            $params[] = $categorySlug;
        }

        switch ($sort) {
            case 'price_asc':  $sql .= " ORDER BY p.price ASC"; break;
            case 'price_desc': $sql .= " ORDER BY p.price DESC"; break;
            case 'newest':     $sql .= " ORDER BY p.created_at DESC"; break;
            default:           $sql .= " ORDER BY p.is_featured DESC, p.id ASC";
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        jsonResponse(['products' => $products, 'count' => count($products)]);

    case 'product_get':
        $slug = $_GET['slug'] ?? '';
        $db = getDB();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                              FROM products p JOIN categories c ON p.category_id = c.id
                              WHERE p.slug = ? AND p.is_active = 1");
        $stmt->execute([$slug]);
        $product = $stmt->fetch();
        if (!$product) jsonResponse(['error' => 'Product not found'], 404);
        jsonResponse(['product' => $product]);

    // ---- SEARCH ----
    case 'search':
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) jsonResponse(['results' => []]);
        $db = getDB();
        $stmt = $db->prepare("SELECT p.id, p.name, p.slug, p.price, p.image_url, c.name as category_name
                              FROM products p JOIN categories c ON p.category_id = c.id
                              WHERE p.is_active = 1 AND (p.name LIKE ? OR p.description LIKE ?)
                              LIMIT 8");
        $like = "%$q%";
        $stmt->execute([$like, $like]);
        jsonResponse(['results' => $stmt->fetchAll()]);

    // ---- CHECKOUT / ORDER ----
    case 'place_order':
        $cart = getCart();
        if (empty($cart)) jsonResponse(['error' => 'Cart is empty'], 400);

        $fullName   = sanitize($_POST['full_name'] ?? '');
        $email      = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $phone      = sanitize($_POST['phone'] ?? '');
        $address    = sanitize($_POST['street_address'] ?? '');
        $city       = sanitize($_POST['city'] ?? '');
        $postal     = sanitize($_POST['postal_code'] ?? '');
        $shipping   = ($_POST['shipping_method'] ?? 'standard') === 'express' ? 'express' : 'standard';
        $notes      = sanitize($_POST['notes'] ?? '');

        if (!$fullName || !$email || !$address || !$city) {
            jsonResponse(['error' => 'Please fill all required fields'], 400);
        }

        $subtotal     = getCartTotal();
        $vat          = $subtotal * VAT_RATE;
        $shippingCost = $shipping === 'express' ? EXPRESS_SHIPPING_COST : 0;
        $total        = $subtotal + $vat + $shippingCost;
        $orderNumber  = generateOrderNumber();
        $userId       = isLoggedIn() ? $_SESSION['user_id'] : null;

        $db = getDB();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("INSERT INTO orders
                (user_id, order_number, full_name, email, phone, street_address, city, postal_code,
                 shipping_method, shipping_cost, subtotal, vat_amount, total_amount, notes)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$userId, $orderNumber, $fullName, $email, $phone, $address, $city, $postal,
                            $shipping, $shippingCost, $subtotal, $vat, $total, $notes]);
            $orderId = $db->lastInsertId();

            $itemStmt = $db->prepare("INSERT INTO order_items
                (order_id, product_id, product_name, product_image, quantity, unit_price, total_price)
                VALUES (?,?,?,?,?,?,?)");
            foreach ($cart as $item) {
                $itemStmt->execute([
                    $orderId, $item['id'], $item['name'], $item['image'],
                    $item['quantity'], $item['price'], $item['price'] * $item['quantity']
                ]);
            }
            $db->commit();
            clearCart();
            jsonResponse(['success' => true, 'order_number' => $orderNumber, 'total' => $total]);
        } catch (Exception $e) {
            $db->rollBack();
            jsonResponse(['error' => 'Order failed. Please try again.'], 500);
        }

    // ---- AUTH ----
    case 'login':
        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            jsonResponse(['success' => true, 'name' => $user['full_name'], 'is_admin' => $user['is_admin']]);
        }
        jsonResponse(['error' => 'Invalid email or password'], 401);

    case 'register':
        $fullName = sanitize($_POST['full_name'] ?? '');
        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $phone    = sanitize($_POST['phone'] ?? '');

        if (!$fullName || !$email || strlen($password) < 8) {
            jsonResponse(['error' => 'Please provide valid details (password min 8 chars)'], 400);
        }
        $db = getDB();
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) jsonResponse(['error' => 'Email already registered'], 409);

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (full_name, email, password_hash, phone) VALUES (?,?,?,?)");
        $stmt->execute([$fullName, $email, $hash, $phone]);
        $_SESSION['user_id'] = $db->lastInsertId();
        jsonResponse(['success' => true, 'name' => $fullName]);

    case 'logout':
        session_destroy();
        jsonResponse(['success' => true]);

    case 'check_auth':
        if (isLoggedIn()) {
            $user = getCurrentUser();
            jsonResponse(['logged_in' => true, 'user' => $user]);
        }
        jsonResponse(['logged_in' => false]);

    // ---- CATEGORIES ----
    case 'categories_get':
        $db = getDB();
        $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order");
        jsonResponse(['categories' => $stmt->fetchAll()]);

    default:
        jsonResponse(['error' => 'Unknown action'], 400);
}
