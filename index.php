<?php
$pageTitle = 'Luxury Blooms Delivered Fresh';
require_once 'php/header.php';
?>

<!-- HERO SECTION -->
<section class="hero">
  <img class="hero-img"
       src="images/hero.png"
       alt="Flora - Luxury Blooms Delivered Fresh">
  <div class="hero-bg"></div>
  <div class="container">
    <div class="hero-content">
      <h1>Luxury Blooms
        <span>Delivered Fresh</span>
      </h1>
      <p>Experience the art of premium flower arrangements. Each bouquet is handcrafted with the finest blooms, bringing elegance and beauty to every special moment.</p>
      <a href="shop.php" class="btn-primary">
        Shop Now
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      </a>
    </div>
  </div>
</section>

<!-- FEATURED CATEGORIES -->
<section style="padding:80px 0; background:var(--white)">
  <div class="container">
    <div style="text-align:center;margin-bottom:48px">
      <h2 style="font-family:var(--font-serif);font-size:clamp(2rem,4vw,3rem);font-weight:400">Our Collections</h2>
      <p style="color:var(--gray);margin-top:10px">Explore our handcrafted arrangements for every occasion</p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px">
      <?php
      $categories = [
        ['slug' => 'flower-bouquets',    'name' => 'Flower Bouquets',    'img' => 'images/rose.png'],
        ['slug' => 'flower-vases',       'name' => 'Flower Vases',       'img' => 'images/rose1.png'],
        ['slug' => 'chocolate-flowers',  'name' => 'Chocolate Flowers',  'img' => 'images/patchi.png'],
        ['slug' => 'gifts-with-flowers', 'name' => 'Gifts with Flowers', 'img' => 'images/cartier.png'],
      ];
      foreach ($categories as $cat): ?>
        <a href="shop.php?category=<?= $cat['slug'] ?>"
           style="display:block;border-radius:var(--radius);overflow:hidden;position:relative;aspect-ratio:3/4;text-decoration:none">
          <img src="<?= $cat['img'] ?>"
               alt="<?= $cat['name'] ?>"
               style="width:100%;height:100%;object-fit:cover;transition:transform 0.5s ease"
               onerror="this.style.background='var(--rose-light)'">
          <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(44,32,34,0.7) 0%,transparent 60%);display:flex;align-items:flex-end;padding:24px">
            <span style="color:white;font-family:var(--font-serif);font-size:1.4rem;font-weight:400">
              <?= $cat['name'] ?>
            </span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- WHY FLORA -->
<section style="background:var(--rose-light);padding:80px 0">
  <div class="container">
    <div style="text-align:center;margin-bottom:48px">
      <h2 style="font-family:var(--font-serif);font-size:clamp(2rem,4vw,3rem);font-weight:400">Why Choose Flora?</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:32px;text-align:center">
      <?php
      $features = [
        ['icon' => '🌸', 'title' => 'Fresh Daily',  'desc' => 'Flowers sourced and arranged daily for maximum freshness'],
        ['icon' => '🚚', 'title' => 'Fast Delivery', 'desc' => 'Same-day express delivery available across Riyadh'],
        ['icon' => '✋', 'title' => 'Handcrafted',   'desc' => 'Every arrangement is crafted with care by expert florists'],
        ['icon' => '💝', 'title' => 'Custom Gifts',  'desc' => 'Add a personal message, chocolates or candles to any order'],
      ];
      foreach ($features as $f): ?>
        <div style="background:white;padding:32px 24px;border-radius:var(--radius);border:1px solid var(--border)">
          <div style="font-size:2.5rem;margin-bottom:14px"><?= $f['icon'] ?></div>
          <h3 style="font-weight:600;margin-bottom:8px;font-size:1rem"><?= $f['title'] ?></h3>
          <p style="color:var(--gray);font-size:0.88rem;line-height:1.7"><?= $f['desc'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once 'php/footer.php'; ?>