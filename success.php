<?php
$pageTitle = 'Order Confirmed';
require_once 'php/header.php';
$orderNumber = htmlspecialchars($_GET['order'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="success-page">
  <div class="success-box">
    <div class="success-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <path d="M20 6L9 17l-5-5"/>
      </svg>
    </div>
    <h2>Order Confirmed! 🌸</h2>
    <p>Thank you for your order. Your beautiful flowers are being prepared with love and care.</p>
    <?php if ($orderNumber): ?>
      <p class="success-order">Order #<?= $orderNumber ?></p>
    <?php endif; ?>
    <p style="color:var(--gray);font-size:0.88rem;margin-bottom:32px">
      A confirmation email has been sent with your order details. Estimated delivery: 2-3 business days.
    </p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
      <a href="shop.php" class="btn-primary">Continue Shopping</a>
      <a href="index.php" style="padding:15px 28px;border:1px solid var(--border);border-radius:50px;font-size:0.9rem;transition:all var(--transition)"
         onmouseover="this.style.background='var(--rose-light)'" onmouseout="this.style.background=''">Back to Home</a>
    </div>
  </div>
</div>

<?php require_once 'php/footer.php'; ?>
