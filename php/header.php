<?php
// php/header.php - Shared header partial
require_once __DIR__ . '/config.php';
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | Flora' : 'Flora - Luxury Blooms Delivered Fresh' ?></title>
  <meta name="description" content="Flora — Premium handcrafted flower bouquets, vases, chocolate flowers and gift sets delivered fresh across Saudi Arabia.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- HEADER -->
<header class="site-header">
  <div class="container">
    <nav class="nav-inner">
      <a href="index.php" class="logo">Flora</a>

      <!-- Desktop Nav Links -->
      <div class="nav-links">
        <a href="shop.php?category=flower-bouquets" class="<?= $currentPage === 'shop' && ($_GET['category'] ?? '') === 'flower-bouquets' ? 'active' : '' ?>">Flower Bouquets</a>
        <a href="shop.php?category=flower-vases"    class="<?= $currentPage === 'shop' && ($_GET['category'] ?? '') === 'flower-vases'    ? 'active' : '' ?>">Flower Vases</a>
        <a href="shop.php?category=chocolate-flowers" class="<?= $currentPage === 'shop' && ($_GET['category'] ?? '') === 'chocolate-flowers' ? 'active' : '' ?>">Chocolate Flowers</a>
        <a href="shop.php?category=gifts-with-flowers" class="<?= $currentPage === 'shop' && ($_GET['category'] ?? '') === 'gifts-with-flowers' ? 'active' : '' ?>">Gifts with Flowers</a>
      </div>

      <!-- Action Buttons -->
      <div class="nav-actions">
        <!-- Search -->
        <button class="nav-btn" id="searchBtn" aria-label="Search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>

        <!-- Auth area -->
        <div id="authArea">
          <button class="nav-btn" onclick="openAuthModal()" title="Login" aria-label="Login">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </button>
        </div>

        <!-- Wishlist -->
        <button class="nav-btn" onclick="openAuthModal()" aria-label="Wishlist">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        </button>

        <!-- Cart -->
        <button class="nav-btn" id="cartBtn" aria-label="Cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          <span class="cart-badge" style="display:<?= $cartCount > 0 ? 'flex' : 'none' ?>"><?= $cartCount ?></span>
        </button>

        <!-- Hamburger (Mobile) -->
        <button class="hamburger" id="hamburger" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </nav>
  </div>
</header>

<!-- MOBILE NAV DRAWER -->
<div class="mobile-nav" id="mobileNav">
  <a href="shop.php?category=flower-bouquets">Flower Bouquets</a>
  <a href="shop.php?category=flower-vases">Flower Vases</a>
  <a href="shop.php?category=chocolate-flowers">Chocolate Flowers</a>
  <a href="shop.php?category=gifts-with-flowers">Gifts with Flowers</a>
</div>

<!-- SEARCH OVERLAY -->
<div class="search-overlay" id="searchOverlay">
  <div class="search-bar">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#999"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="searchInput" placeholder="Search flowers, bouquets..." autocomplete="off">
    <button id="closeSearch" style="color:#999">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="search-results container" id="searchResults"></div>
</div>

<!-- CART DRAWER -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-drawer" id="cartDrawer">
  <div class="cart-header">
    <h3>Your Cart</h3>
    <button class="cart-close" id="cartClose" aria-label="Close cart">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      <p>Your cart is empty</p>
    </div>
  </div>
  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-totals">
      <div class="cart-total-row"><span>Subtotal</span><span id="cartSubtotal">SAR 0.00</span></div>
      <div class="cart-total-row"><span>VAT (15%)</span><span id="cartVAT">SAR 0.00</span></div>
      <div class="cart-total-row"><span>Delivery</span><span style="color:#27ae60">Free</span></div>
      <div class="cart-total-row total"><span>Total</span><span id="cartTotal">SAR 0.00</span></div>
    </div>
    <button class="btn-checkout" id="checkoutBtn">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      Proceed to Checkout
    </button>
  </div>
</div>

<!-- PRODUCT MODAL -->
<div class="modal-overlay" id="productModal">
  <div class="modal-box">
    <img class="modal-img" id="modalImg" src="" alt="">
    <div class="modal-body">
      <button class="modal-close" onclick="closeProductModal()" aria-label="Close">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
      <div class="modal-category" id="modalCategory"></div>
      <h2 class="modal-name" id="modalName"></h2>
      <div class="modal-price" id="modalPrice"></div>
      <p class="modal-desc" id="modalDesc"></p>
      <div class="qty-wrap">
        <div class="qty-control">
          <button class="qty-btn" id="qtyMinus">−</button>
          <input class="qty-input" type="number" id="modalQty" value="1" min="1" max="99">
          <button class="qty-btn" id="qtyPlus">+</button>
        </div>
      </div>
      <button class="btn-add-modal" id="modalAddBtn" data-product-id="">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        Add to Cart
      </button>
    </div>
  </div>
</div>

<!-- AUTH MODAL -->
<div class="auth-modal" id="authModal">
  <div class="auth-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <span class="logo" style="font-size:1.5rem">Flora</span>
      <button class="auth-close" onclick="closeAuthModal()" aria-label="Close">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <h2 class="auth-title" id="authTitle">Welcome Back</h2>
    <p class="auth-subtitle" id="authSubtitle">Sign in to your Flora account</p>

    <!-- Login Form -->
    <form class="auth-form" id="loginForm">
      <input class="form-input" type="email" id="loginEmail" placeholder="Email Address" required>
      <input class="form-input" type="password" id="loginPassword" placeholder="Password" required>
      <button type="submit" class="btn-primary" style="width:100%;justify-content:center;border-radius:50px;padding:14px">Sign In</button>
      <div class="auth-toggle">Don't have an account? <a onclick="switchAuthTab('register')">Create one</a></div>
    </form>

    <!-- Register Form -->
    <form class="auth-form hidden" id="registerForm">
      <input class="form-input" type="text" id="regName" placeholder="Full Name" required>
      <input class="form-input" type="email" id="regEmail" placeholder="Email Address" required>
      <input class="form-input" type="tel" id="regPhone" placeholder="Phone Number">
      <input class="form-input" type="password" id="regPassword" placeholder="Password (min 8 chars)" required minlength="8">
      <button type="submit" class="btn-primary" style="width:100%;justify-content:center;border-radius:50px;padding:14px">Create Account</button>
      <div class="auth-toggle">Already have an account? <a onclick="switchAuthTab('login')">Sign in</a></div>
    </form>
  </div>
</div>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toastContainer"></div>

<style>.hidden{display:none!important}</style>
