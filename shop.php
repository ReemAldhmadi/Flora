<?php
$pageTitle = 'Our Collection';
require_once 'php/header.php';

// Get initial category from URL
$initCategory = htmlspecialchars($_GET['category'] ?? 'all', ENT_QUOTES, 'UTF-8');
?>

<!-- SECTION BANNER -->
<div class="section-banner">
  <h2>Our Collection</h2>
  <p>Discover the perfect flowers for every moment</p>
</div>

<!-- SHOP LAYOUT -->
<div class="container">
  <div class="shop-layout">

    <!-- FILTERS SIDEBAR -->
    <aside class="filters-sidebar">
      <div class="filters-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        Filters
      </div>

      <div class="filter-group">
        <h4>Category</h4>
        <div class="filter-cats">
          <button class="cat-btn <?= $initCategory === 'all' ? 'active' : '' ?>" data-category="all">All</button>
          <button class="cat-btn <?= $initCategory === 'flower-bouquets' ? 'active' : '' ?>"   data-category="flower-bouquets">Flower Bouquets</button>
          <button class="cat-btn <?= $initCategory === 'flower-vases' ? 'active' : '' ?>"      data-category="flower-vases">Flower Vases</button>
          <button class="cat-btn <?= $initCategory === 'chocolate-flowers' ? 'active' : '' ?>" data-category="chocolate-flowers">Chocolate Flowers</button>
          <button class="cat-btn <?= $initCategory === 'gifts-with-flowers' ? 'active' : '' ?>" data-category="gifts-with-flowers">Gifts with Flowers</button>
        </div>
      </div>

      <hr class="divider">

      <div class="filter-group">
        <h4>Price Range</h4>
        <div class="price-range-wrap">
          <input type="range" id="priceRange" min="0" max="1000" value="1000" step="10">
          <div class="price-labels">
            <span>SAR 0</span>
            <span>SAR <span id="maxPriceLabel">300</span></span>
          </div>
        </div>
      </div>
    </aside>

    <!-- PRODUCTS AREA -->
    <div class="products-area">
      <div class="products-header">
        <div class="products-count" id="productsCount">Loading...</div>
        <select class="sort-select" id="sortSelect">
          <option value="featured">Featured</option>
          <option value="price_asc">Price: Low to High</option>
          <option value="price_desc">Price: High to Low</option>
          <option value="newest">Newest</option>
        </select>
      </div>

      <div class="product-grid" id="productGrid">
        <!-- Loaded dynamically by JS -->
      </div>
    </div>

  </div>
</div>

<script>
// Set initial category from PHP
document.addEventListener('DOMContentLoaded', () => {
  const initCat = '<?= $initCategory ?>';
  if (initCat && initCat !== 'all') {
    currentCategory = initCat;
    // Trigger the correct button
    const btn = document.querySelector(`.cat-btn[data-category="${initCat}"]`);
    if (btn) {
      document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    }
  }
});
</script>

<?php require_once 'php/footer.php'; ?>
