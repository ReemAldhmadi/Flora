<?php
// =============================================
// Flora Config - config.php
// =============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'flora_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Flora');
define('SITE_URL', 'http://localhost/flora');
define('VAT_RATE', 0.15); // 15% VAT (Saudi Arabia)
define('EXPRESS_SHIPPING_COST', 50.00);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PDO Database connection
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// Cart helpers (session-based)
function getCart(): array {
    return $_SESSION['cart'] ?? [];
}

function getCartCount(): int {
    $cart = getCart();
    return array_sum(array_column($cart, 'quantity'));
}

function getCartTotal(): float {
    $cart = getCart();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function addToCart(int $productId, int $qty = 1): bool {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, price, image_url FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) return false;

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $qty;
    } else {
        $_SESSION['cart'][$productId] = [
            'id'       => $product['id'],
            'name'     => $product['name'],
            'price'    => $product['price'],
            'image'    => $product['image_url'],
            'quantity' => $qty,
        ];
    }
    return true;
}

function removeFromCart(int $productId): void {
    unset($_SESSION['cart'][$productId]);
}

function updateCartQty(int $productId, int $qty): void {
    if ($qty <= 0) {
        removeFromCart($productId);
    } elseif (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] = $qty;
    }
}

function clearCart(): void {
    $_SESSION['cart'] = [];
}

// Auth helpers
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id, full_name, email, phone, is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

// Utility
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatPrice(float $price): string {
    return 'SAR ' . number_format($price, 2);
}

function generateOrderNumber(): string {
    return 'FLR-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
