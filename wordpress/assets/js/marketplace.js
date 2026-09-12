/**
 * Digital Marketplace WordPress Theme - Client Interactivity
 * Handles mobile menu toggle, product gallery previews, cart quantity updates,
 * coupon calculation, client-side form validation, and account tab navigation.
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // 1. Mobile Menu Toggle
  const mobileToggle = document.getElementById('wp-mobile-menu-toggle');
  const mobilePanel = document.getElementById('wp-mobile-nav-panel');

  if (mobileToggle && mobilePanel) {
    mobileToggle.addEventListener('click', function () {
      const isOpen = mobilePanel.classList.toggle('is-open');
      mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  // 2. Product Detail Image Gallery Thumbnail Selector
  const mainGalleryImage = document.getElementById('main-gallery-image');
  const thumbButtons = document.querySelectorAll('.product-thumb-item');

  if (mainGalleryImage && thumbButtons.length > 0) {
    thumbButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const fullSrc = btn.getAttribute('data-full-image');
        if (fullSrc) {
          mainGalleryImage.setAttribute('src', fullSrc);
          thumbButtons.forEach(function (b) { b.classList.remove('active'); });
          btn.classList.add('active');
        }
      });
    });
  }

  // 3. Quantity Stepper Controls (Single Product & Cart)
  const qtySteppers = document.querySelectorAll('.qty-stepper');
  qtySteppers.forEach(function (stepper) {
    const decBtn = stepper.querySelector('.qty-btn-minus');
    const incBtn = stepper.querySelector('.qty-btn-plus');
    const input = stepper.querySelector('.qty-input') || stepper.querySelector('.qty-val');

    if (decBtn && incBtn && input) {
      decBtn.addEventListener('click', function (e) {
        e.preventDefault();
        let val = parseInt(input.value || input.textContent, 10) || 1;
        if (val > 1) {
          val -= 1;
          if (input.tagName === 'INPUT') {
            input.value = val;
            input.dispatchEvent(new Event('change', { bubbles: true }));
          } else {
            input.textContent = val;
          }
          updateItemRowSubtotal(stepper, val);
        }
      });

      incBtn.addEventListener('click', function (e) {
        e.preventDefault();
        let val = parseInt(input.value || input.textContent, 10) || 1;
        val += 1;
        if (input.tagName === 'INPUT') {
          input.value = val;
          input.dispatchEvent(new Event('change', { bubbles: true }));
        } else {
          input.textContent = val;
        }
        updateItemRowSubtotal(stepper, val);
      });
    }
  });

  function updateItemRowSubtotal(stepper, qty) {
    const row = stepper.closest('.cart-item-row');
    if (!row) return;
    const unitPrice = parseFloat(row.getAttribute('data-price')) || 0;
    const subtotalEl = row.querySelector('.cart-item-subtotal');
    if (subtotalEl) {
      subtotalEl.textContent = '$' + (unitPrice * qty).toFixed(2);
    }
    recalculateCartTotals();
  }

  function recalculateCartTotals() {
    const rows = document.querySelectorAll('.cart-item-row');
    let subtotal = 0;
    rows.forEach(function (r) {
      const price = parseFloat(r.getAttribute('data-price')) || 0;
      const qtyEl = r.querySelector('.qty-val') || r.querySelector('.qty-input');
      const qty = parseInt(qtyEl ? (qtyEl.value || qtyEl.textContent) : 1, 10) || 1;
      subtotal += price * qty;
    });

    const subtotalDisplay = document.getElementById('cart-display-subtotal');
    const taxDisplay = document.getElementById('cart-display-tax');
    const totalDisplay = document.getElementById('cart-display-total');
    const discountEl = document.getElementById('cart-discount-val');

    let discount = 0;
    if (discountEl && discountEl.dataset.discount) {
      discount = parseFloat(discountEl.dataset.discount) || 0;
    }

    const tax = subtotal * 0.08;
    const total = Math.max(0, subtotal + tax - discount);

    if (subtotalDisplay) subtotalDisplay.textContent = '$' + subtotal.toFixed(2);
    if (taxDisplay) taxDisplay.textContent = '$' + tax.toFixed(2);
    if (totalDisplay) totalDisplay.textContent = '$' + total.toFixed(2);
  }

  // 4. Cart Promo Code Validation
  const couponForm = document.getElementById('marketplace-coupon-form');
  if (couponForm) {
    couponForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const input = document.getElementById('coupon-input');
      const feedback = document.getElementById('coupon-feedback');
      const discountEl = document.getElementById('cart-discount-val');
      if (!input) return;

      const code = input.value.trim().toUpperCase();
      if (code === 'WELCOME10' || code === 'SAVE10') {
        if (discountEl) {
          discountEl.dataset.discount = '10.00';
          discountEl.textContent = '-$10.00';
          const discountRow = document.getElementById('cart-discount-row');
          if (discountRow) discountRow.style.display = 'flex';
        }
        if (feedback) {
          feedback.textContent = 'Coupon code "' + code + '" applied (-$10.00)!';
          feedback.style.color = '#059669';
        }
        recalculateCartTotals();
      } else {
        if (feedback) {
          feedback.textContent = 'Invalid promo code. Try "WELCOME10".';
          feedback.style.color = '#dc2626';
        }
      }
    });
  }

  // 5. Client Form Validation for Checkout
  const checkoutForm = document.getElementById('wp-checkout-form');
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function (e) {
      let hasError = false;
      const requiredFields = checkoutForm.querySelectorAll('[required]');

      requiredFields.forEach(function (field) {
        const errorMsg = field.parentElement.querySelector('.field-error');
        if (!field.value.trim()) {
          field.style.borderColor = '#ef4444';
          if (errorMsg) errorMsg.style.display = 'block';
          hasError = true;
        } else {
          field.style.borderColor = '';
          if (errorMsg) errorMsg.style.display = 'none';
        }
      });

      if (hasError) {
        e.preventDefault();
        const firstErr = checkoutForm.querySelector('.field-error[style*="block"]');
        if (firstErr) {
          firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
  }

  // 6. User Account Tabs Navigation
  const tabButtons = document.querySelectorAll('.account-tab-nav button');
  const tabPanels = document.querySelectorAll('.account-tab-panel');

  if (tabButtons.length > 0 && tabPanels.length > 0) {
    tabButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = btn.getAttribute('data-target');
        tabButtons.forEach(function (b) { b.classList.remove('active'); });
        tabPanels.forEach(function (p) { p.style.display = 'none'; });

        btn.classList.add('active');
        const targetPanel = document.getElementById(targetId);
        if (targetPanel) {
          targetPanel.style.display = 'block';
        }
      });
    });
  }
});
