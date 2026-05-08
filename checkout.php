<?php
$pageTitle = 'Checkout';
require_once 'php/header.php';
?>

<!-- SECTION BANNER -->
<div class="section-banner">
  <h2>Checkout</h2>
</div>

<div class="container" id="checkoutPage">
  <div class="checkout-layout">

    <!-- LEFT: FORMS -->
    <div class="checkout-form-section">
      <form id="checkoutForm">

        <!-- Shipping Information -->
        <div class="form-card">
          <div class="form-card-title">
            <div class="icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
              </svg>
            </div>
            Shipping Information
          </div>
          <div class="form-grid">
            <input class="form-input" type="text" id="fullName" name="full_name" placeholder="Full Name" required>
            <input class="form-input" type="email" id="email" name="email" placeholder="Email Address" required>
            <input class="form-input full" type="tel" id="phone" name="phone" placeholder="Phone Number" required>
            <input class="form-input full" type="text" id="streetAddress" name="street_address" placeholder="Street Address" required>
            <input class="form-input" type="text" id="city" name="city" placeholder="City" required>
            <input class="form-input" type="text" id="postalCode" name="postal_code" placeholder="Postal Code">
          </div>
        </div>

        <!-- Shipping Method -->
        <div class="form-card">
          <div class="form-card-title">
            <div class="icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
              </svg>
            </div>
            Shipping Method
          </div>
          <div class="shipping-options">
            <label class="shipping-option selected">
              <input type="radio" name="shipping" value="standard" checked>
              <div class="shipping-option-info">
                <strong>Standard Delivery</strong>
                <span>2-3 business days</span>
              </div>
              <span class="shipping-option-price free">Free</span>
            </label>
            <label class="shipping-option">
              <input type="radio" name="shipping" value="express">
              <div class="shipping-option-info">
                <strong>Express Delivery</strong>
                <span>Same day delivery</span>
              </div>
              <span class="shipping-option-price">SAR 50</span>
            </label>
          </div>
        </div>

        <!-- Notes -->
        <div class="form-card">
          <div class="form-card-title">
            <div class="icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
              </svg>
            </div>
            Order Notes (Optional)
          </div>
          <textarea class="form-input" id="notes" name="notes" placeholder="Special instructions, delivery notes, card message..." 
                    style="min-height:100px;resize:vertical;line-height:1.6"></textarea>
        </div>

      </form>
    </div>

    <!-- RIGHT: ORDER SUMMARY -->
    <div class="order-summary-card">
      <h3>Order Summary</h3>
      <div class="summary-items" id="summaryItems">
        <p style="color:var(--gray);text-align:center;padding:20px">Loading...</p>
      </div>
      <hr class="summary-divider">
      <div class="summary-row"><span>Subtotal</span><span id="summarySubtotal">—</span></div>
      <div class="summary-row"><span>VAT (15%)</span><span id="summaryVAT">—</span></div>
      <div class="summary-row"><span>Delivery</span><span id="summaryDelivery" style="color:#27ae60">Free</span></div>
      <div class="summary-row total"><span>Total</span><span id="summaryTotal">—</span></div>
      <button type="submit" form="checkoutForm" class="btn-place-order" id="placeOrderBtn">
        Place Order
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      </button>
    </div>

  </div>
</div>

<?php require_once 'php/footer.php'; ?>
