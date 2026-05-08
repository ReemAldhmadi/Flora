/* =============================================
   Flora - Main JavaScript (app.js)
   ============================================= */

const API = 'php/api.php';

// =============================================
// UTILITY HELPERS
// =============================================
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

function fmt(amount) {
  return 'SAR ' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

async function post(action, data = {}) {
  const fd = new FormData();
  fd.append('action', action);
  Object.entries(data).forEach(([k, v]) => fd.append(k, v));
  const res = await fetch(API, { method: 'POST', body: fd });
  return res.json();
}

async function get(action, params = {}) {
  const q = new URLSearchParams({ action, ...params });
  const res = await fetch(`${API}?${q}`);
  return res.json();
}

// =============================================
// TOAST NOTIFICATIONS
// =============================================
function toast(msg, type = 'success') {
  const container = document.getElementById('toastContainer');
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  const icon = type === 'success'
    ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 6L9 17l-5-5" stroke-width="2" stroke-linecap="round"/></svg>`
    : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 8v4m0 4h.01" stroke-width="2" stroke-linecap="round"/></svg>`;
  el.innerHTML = icon + `<span>${msg}</span>`;
  container.appendChild(el);
  setTimeout(() => el.remove(), 3100);
}

// =============================================
// CART STATE & UI
// =============================================
let cartData = { items: [], count: 0, subtotal: 0, vat: 0, total: 0 };

async function refreshCart() {
  cartData = await get('cart_get');
  renderCart();
  updateCartBadge(cartData.count);
}

function updateCartBadge(count) {
  $$('.cart-badge').forEach(el => {
    el.textContent = count;
    el.style.display = count > 0 ? 'flex' : 'none';
  });
}

function renderCart() {
  const container = document.getElementById('cartItems');
  const footer    = document.getElementById('cartFooter');
  if (!container) return;

  if (!cartData.items || cartData.items.length === 0) {
    container.innerHTML = `
      <div class="cart-empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        <p>Your cart is empty</p>
        <small>Add some flowers to brighten your day!</small>
      </div>`;
    if (footer) footer.style.display = 'none';
    return;
  }

  if (footer) footer.style.display = 'block';

  container.innerHTML = cartData.items.map(item => `
    <div class="cart-item" data-id="${item.id}">
      <img class="cart-item-img" src="${item.image || 'https://images.unsplash.com/photo-1487530811015-780780169993?w=200'}" 
           alt="${item.name}" onerror="this.src='https://images.unsplash.com/photo-1487530811015-780780169993?w=200'">
      <div class="cart-item-info">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-price">${fmt(item.price)}</div>
        <div class="cart-qty-row">
          <div class="cart-qty">
            <button onclick="changeCartQty(${item.id}, ${item.quantity - 1})">−</button>
            <span>${item.quantity}</span>
            <button onclick="changeCartQty(${item.id}, ${item.quantity + 1})">+</button>
          </div>
          <button class="cart-remove" onclick="removeCartItem(${item.id})">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
            Remove
          </button>
        </div>
      </div>
    </div>`).join('');

  document.getElementById('cartSubtotal').textContent = fmt(cartData.subtotal);
  document.getElementById('cartVAT').textContent      = fmt(cartData.vat);
  document.getElementById('cartTotal').textContent    = fmt(cartData.total);
}

async function changeCartQty(productId, newQty) {
  const res = await post('cart_update', { product_id: productId, qty: newQty });
  if (res.success) {
    cartData = await get('cart_get');
    renderCart();
    updateCartBadge(res.cart_count);
  }
}

async function removeCartItem(productId) {
  const res = await post('cart_remove', { product_id: productId });
  if (res.success) {
    cartData = await get('cart_get');
    renderCart();
    updateCartBadge(res.cart_count);
    toast('Item removed from cart');
  }
}

async function addToCart(productId, qty = 1) {
  const res = await post('cart_add', { product_id: productId, qty });
  if (res.success) {
    updateCartBadge(res.cart_count);
    toast('Added to cart 🌸');
    openCart();
  } else {
    toast('Failed to add item', 'error');
  }
}

function openCart() {
  document.getElementById('cartDrawer').classList.add('open');
  document.getElementById('cartOverlay').classList.add('show');
  document.body.style.overflow = 'hidden';
  refreshCart();
}

function closeCart() {
  document.getElementById('cartDrawer').classList.remove('open');
  document.getElementById('cartOverlay').classList.remove('show');
  document.body.style.overflow = '';
}

// =============================================
// PRODUCT MODAL
// =============================================
function openProductModal(product) {
  const modal = document.getElementById('productModal');
  $('#modalImg', modal).src = product.image_url || 'https://images.unsplash.com/photo-1487530811015-780780169993?w=600';
  $('#modalImg', modal).alt = product.name;
  $('#modalCategory', modal).textContent = product.category_name || '';
  $('#modalName', modal).textContent = product.name;
  $('#modalPrice', modal).textContent = fmt(product.price);
  $('#modalDesc', modal).textContent = product.description || '';
  $('#modalQty', modal).value = 1;
  $('#modalAddBtn', modal).dataset.productId = product.id;
  $('#modalAddBtn', modal).classList.remove('added');
  $('#modalAddBtn', modal).innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
    Add to Cart`;
  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeProductModal() {
  document.getElementById('productModal').classList.remove('show');
  document.body.style.overflow = '';
}

// =============================================
// PRODUCT GRID
// =============================================
let allProducts = [];
let currentCategory = 'all';
let currentSort = 'featured';
let maxPrice = 300;

function renderProducts(products) {
  const grid = document.getElementById('productGrid');
  const count = document.getElementById('productsCount');
  if (!grid) return;

  if (count) count.textContent = `${products.length} Product${products.length !== 1 ? 's' : ''}`;

  if (products.length === 0) {
    grid.innerHTML = `
      <div class="no-products">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <p>No products found</p>
        <small>Try adjusting your filters</small>
      </div>`;
    return;
  }

  grid.innerHTML = products.map(p => `
    <div class="product-card" onclick="openProductModal(${JSON.stringify(p).replace(/"/g, '&quot;')})">
      <div class="product-img-wrap">
        <img src="${p.image_url || 'https://images.unsplash.com/photo-1487530811015-780780169993?w=600'}" 
             alt="${p.name}" loading="lazy"
             onerror="this.src='https://images.unsplash.com/photo-1487530811015-780780169993?w=600'">
        <button class="wishlist-btn" onclick="event.stopPropagation(); toggleWishlist(this, ${p.id})" aria-label="Add to wishlist">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
          </svg>
        </button>
      </div>
      <div class="product-info">
        <div class="product-category">${p.category_name || ''}</div>
        <div class="product-name">${p.name}</div>
        <div class="product-footer">
          <div class="product-price">
            ${fmt(p.price)}
            ${p.original_price ? `<span class="original">${fmt(p.original_price)}</span>` : ''}
          </div>
          <button class="add-cart-btn" onclick="event.stopPropagation(); addToCart(${p.id})" aria-label="Add to cart">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
          </button>
        </div>
      </div>
    </div>`).join('');
}

function showSkeletons(n = 6) {
  const grid = document.getElementById('productGrid');
  if (!grid) return;
  grid.innerHTML = Array(n).fill(`
    <div class="skeleton-card">
      <div class="skeleton skeleton-img"></div>
      <div class="skeleton-body">
        <div class="skeleton skeleton-line" style="width:50%"></div>
        <div class="skeleton skeleton-line" style="width:80%"></div>
        <div class="skeleton skeleton-line" style="width:40%"></div>
      </div>
    </div>`).join('');
}

async function loadProducts() {
  showSkeletons();
  const res = await get('products_get', {
    category: currentCategory,
    sort: currentSort,
    min_price: 0,
    max_price: maxPrice
  });
  allProducts = res.products || [];
  renderProducts(allProducts);
}

// =============================================
// WISHLIST
// =============================================
async function toggleWishlist(btn, productId) {
  const res = await post('wishlist_toggle', { product_id: productId });
  if (res.login_required) {
    openAuthModal();
    return;
  }
  if (res.success) {
    btn.classList.toggle('active', res.wishlisted);
    toast(res.wishlisted ? 'Added to wishlist ♡' : 'Removed from wishlist');
  }
}

// =============================================
// SEARCH
// =============================================
let searchTimeout;

function initSearch() {
  const searchOverlay = document.getElementById('searchOverlay');
  const searchInput   = document.getElementById('searchInput');
  const searchResults = document.getElementById('searchResults');
  const searchBtn     = document.getElementById('searchBtn');
  const closeSearch   = document.getElementById('closeSearch');

  if (!searchBtn) return;

  searchBtn.addEventListener('click', () => {
    searchOverlay.classList.add('show');
    searchInput?.focus();
  });

  closeSearch?.addEventListener('click', () => searchOverlay.classList.remove('show'));

  searchInput?.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
      const q = searchInput.value.trim();
      if (q.length < 2) { searchResults.innerHTML = ''; return; }
      const res = await get('search', { q });
      searchResults.innerHTML = (res.results || []).map(p => `
        <div class="search-result-item" onclick="handleSearchClick('${p.slug}')">
          <img src="${p.image_url || ''}" alt="${p.name}" onerror="this.style.display='none'">
          <div>
            <div class="name">${p.name}</div>
            <div class="price">${fmt(p.price)}</div>
          </div>
        </div>`).join('') || '<div style="padding:12px;color:#999">No results found</div>';
    }, 300);
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') searchOverlay.classList.remove('show');
  });
}

function handleSearchClick(slug) {
  document.getElementById('searchOverlay')?.classList.remove('show');
  // Navigate to shop page with slug
  window.location.href = `shop.php?product=${slug}`;
}

// =============================================
// AUTH MODAL
// =============================================
function openAuthModal() {
  document.getElementById('authModal')?.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeAuthModal() {
  document.getElementById('authModal')?.classList.remove('show');
  document.body.style.overflow = '';
}

function switchAuthTab(tab) {
  const loginForm    = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const authTitle    = document.getElementById('authTitle');
  const authSubtitle = document.getElementById('authSubtitle');

  if (tab === 'login') {
    loginForm?.classList.remove('hidden');
    registerForm?.classList.add('hidden');
    if (authTitle)    authTitle.textContent    = 'Welcome Back';
    if (authSubtitle) authSubtitle.textContent = 'Sign in to your Flora account';
  } else {
    loginForm?.classList.add('hidden');
    registerForm?.classList.remove('hidden');
    if (authTitle)    authTitle.textContent    = 'Create Account';
    if (authSubtitle) authSubtitle.textContent = 'Join Flora for exclusive offers';
  }
}

async function handleLogin(e) {
  e.preventDefault();
  const email    = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;
  const res = await post('login', { email, password });
  if (res.success) {
    toast(`Welcome back, ${res.name}! 🌸`);
    closeAuthModal();
    updateAuthUI(true, res.name);
  } else {
    toast(res.error || 'Login failed', 'error');
  }
}

async function handleRegister(e) {
  e.preventDefault();
  const full_name = document.getElementById('regName').value;
  const email     = document.getElementById('regEmail').value;
  const password  = document.getElementById('regPassword').value;
  const phone     = document.getElementById('regPhone')?.value || '';
  const res = await post('register', { full_name, email, password, phone });
  if (res.success) {
    toast(`Welcome to Flora, ${res.name}! 🌸`);
    closeAuthModal();
    updateAuthUI(true, res.name);
  } else {
    toast(res.error || 'Registration failed', 'error');
  }
}

async function handleLogout() {
  await post('logout');
  updateAuthUI(false);
  toast('Logged out successfully');
}

function updateAuthUI(loggedIn, name = '') {
  const authArea = document.getElementById('authArea');
  if (!authArea) return;
  if (loggedIn) {
    authArea.innerHTML = `
      <span style="font-size:0.85rem;color:#666">Hi, ${name}</span>
      <button class="nav-btn" onclick="handleLogout()" title="Logout">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </button>`;
  } else {
    authArea.innerHTML = `
      <button class="nav-btn" onclick="openAuthModal()" title="Login">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </button>`;
  }
}

// =============================================
// CHECKOUT
// =============================================
function initCheckout() {
  const checkoutPage = document.getElementById('checkoutPage');
  if (!checkoutPage) return;

  // Render order summary from cart
  renderOrderSummary();

  // Shipping method toggle
  $$('.shipping-option').forEach(opt => {
    opt.addEventListener('click', () => {
      $$('.shipping-option').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      const radio = opt.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
      updateOrderTotal();
    });
  });

  // Place order
  const form = document.getElementById('checkoutForm');
  form?.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.textContent = 'Placing Order...';

    const data = {
      full_name:       document.getElementById('fullName').value,
      email:           document.getElementById('email').value,
      phone:           document.getElementById('phone').value,
      street_address:  document.getElementById('streetAddress').value,
      city:            document.getElementById('city').value,
      postal_code:     document.getElementById('postalCode').value,
      shipping_method: document.querySelector('input[name="shipping"]:checked')?.value || 'standard',
      notes:           document.getElementById('notes')?.value || ''
    };

    const res = await post('place_order', data);
    if (res.success) {
      window.location.href = `success.php?order=${res.order_number}`;
    } else {
      toast(res.error || 'Order failed. Please try again.', 'error');
      btn.disabled = false;
      btn.textContent = 'Place Order';
    }
  });
}

async function renderOrderSummary() {
  const cart = await get('cart_get');
  const summaryItems = document.getElementById('summaryItems');
  if (!summaryItems) return;

  if (!cart.items || cart.items.length === 0) {
    window.location.href = 'index.php';
    return;
  }

  summaryItems.innerHTML = cart.items.map(item => `
    <div class="summary-item">
      <img src="${item.image || 'https://images.unsplash.com/photo-1487530811015-780780169993?w=100'}" 
           alt="${item.name}"
           onerror="this.src='https://images.unsplash.com/photo-1487530811015-780780169993?w=100'">
      <div class="summary-item-info">
        <div class="summary-item-name">${item.name}</div>
        <div class="summary-item-qty">Qty: ${item.quantity}</div>
      </div>
      <div class="summary-item-price">${fmt(item.price * item.quantity)}</div>
    </div>`).join('');

  document.getElementById('summarySubtotal').textContent = fmt(cart.subtotal);
  document.getElementById('summaryVAT').textContent      = fmt(cart.vat);
  document.getElementById('summaryTotal').textContent    = fmt(cart.total);
  document.getElementById('summaryDelivery').textContent = 'Free';
}

function updateOrderTotal() {
  const isExpress = document.querySelector('input[name="shipping"]:checked')?.value === 'express';
  const shippingCost = isExpress ? 50 : 0;
  const subtotal = cartData.subtotal || 0;
  const vat = subtotal * 0.15;
  const total = subtotal + vat + shippingCost;

  const deliveryEl = document.getElementById('summaryDelivery');
  const totalEl    = document.getElementById('summaryTotal');
  if (deliveryEl) deliveryEl.textContent = shippingCost > 0 ? fmt(shippingCost) : 'Free';
  if (totalEl)    totalEl.textContent    = fmt(total);
}

// =============================================
// NAVIGATION (MOBILE)
// =============================================
function initMobileNav() {
  const hamburger  = document.getElementById('hamburger');
  const mobileNav  = document.getElementById('mobileNav');

  hamburger?.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    mobileNav?.classList.toggle('show');
  });

  // Close mobile nav when link clicked
  $$('#mobileNav a').forEach(a => {
    a.addEventListener('click', () => {
      hamburger?.classList.remove('open');
      mobileNav?.classList.remove('show');
    });
  });
}

// =============================================
// PRICE RANGE FILTER
// =============================================
function initPriceFilter() {
  const slider = document.getElementById('priceRange');
  const label  = document.getElementById('maxPriceLabel');

  slider?.addEventListener('input', () => {
    maxPrice = parseInt(slider.value);
    if (label) label.textContent = `SAR ${maxPrice}`;
    const pct = ((maxPrice - 0) / (1000 - 0)) * 100;
    slider.style.background = `linear-gradient(to right, var(--rose) ${pct}%, var(--border) ${pct}%)`;
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(loadProducts, 400);
  });
}

// =============================================
// CATEGORY FILTER
// =============================================
function initCategoryFilter() {
  $$('.cat-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      $$('.cat-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCategory = btn.dataset.category;
      loadProducts();
    });
  });
}

// =============================================
// SORT
// =============================================
function initSort() {
  const sortEl = document.getElementById('sortSelect');
  sortEl?.addEventListener('change', () => {
    currentSort = sortEl.value;
    loadProducts();
  });
}

// =============================================
// MODAL QTY CONTROLS
// =============================================
function initModalQty() {
  const qtyMinus = document.getElementById('qtyMinus');
  const qtyPlus  = document.getElementById('qtyPlus');
  const qtyInput = document.getElementById('modalQty');

  qtyMinus?.addEventListener('click', () => {
    const v = parseInt(qtyInput.value);
    if (v > 1) qtyInput.value = v - 1;
  });

  qtyPlus?.addEventListener('click', () => {
    qtyInput.value = parseInt(qtyInput.value) + 1;
  });

  document.getElementById('modalAddBtn')?.addEventListener('click', async function () {
    const productId = parseInt(this.dataset.productId);
    const qty = parseInt(qtyInput.value);
    await addToCart(productId, qty);
    this.classList.add('added');
    this.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Added!`;
    setTimeout(() => closeProductModal(), 1200);
  });
}

// =============================================
// CHECK AUTH STATE ON LOAD
// =============================================
async function checkAuthState() {
  const res = await get('check_auth');
  if (res.logged_in) {
    updateAuthUI(true, res.user.full_name);
  } else {
    updateAuthUI(false);
  }
}

// =============================================
// INIT
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  // Cart
  document.getElementById('cartBtn')?.addEventListener('click', openCart);
  document.getElementById('cartClose')?.addEventListener('click', closeCart);
  document.getElementById('cartOverlay')?.addEventListener('click', closeCart);
  document.getElementById('checkoutBtn')?.addEventListener('click', () => {
    closeCart();
    window.location.href = 'checkout.php';
  });

  // Product modal
  document.getElementById('modalClose')?.addEventListener('click', closeProductModal);
  document.getElementById('productModal')?.addEventListener('click', function (e) {
    if (e.target === this) closeProductModal();
  });

  // Auth modal
  document.getElementById('authClose')?.addEventListener('click', closeAuthModal);
  document.getElementById('authModal')?.addEventListener('click', function (e) {
    if (e.target === this) closeAuthModal();
  });
  document.getElementById('loginForm')?.addEventListener('submit', handleLogin);
  document.getElementById('registerForm')?.addEventListener('submit', handleRegister);

  // Nav
  initMobileNav();
  initSearch();
  initPriceFilter();
  initCategoryFilter();
  initSort();
  initModalQty();
  initCheckout();

  // Load initial cart badge
  refreshCart();

  // Check auth
  checkAuthState();

  // Load products if on shop page
  if (document.getElementById('productGrid')) {
    loadProducts();
  }
});
