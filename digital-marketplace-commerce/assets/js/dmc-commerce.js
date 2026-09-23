/**
 * Digital Marketplace Commerce (DMC) - Front-End Commerce Interactions
 * Handles AJAX Add-to-Cart, Quantity Stepper, Item Removal, Cart Badge synchronization,
 * and Instant Buy Now direct redirection.
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  if (typeof dmcCommerceData === 'undefined') {
    return;
  }

  const ajaxUrl = dmcCommerceData.ajaxUrl;
  const cartNonce = dmcCommerceData.cartNonce;
  const cartUrl = dmcCommerceData.cartUrl;
  const checkoutUrl = dmcCommerceData.checkoutUrl;

  // Helper: Update Header Cart Badge
  function updateHeaderCartBadge(count) {
    const badge = document.getElementById('nav-cart-badge');
    if (badge) {
      badge.textContent = count;
    }
  }

  // 1. AJAX Add to Cart (Product Cards & Single Product page)
  document.addEventListener('click', function (e) {
    const addBtn = e.target.closest('.dmc-add-to-cart-btn');
    if (!addBtn) return;

    e.preventDefault();
    const productId = addBtn.getAttribute('data-product-id');
    if (!productId) return;

    // Check if quantity selector exists on page
    const qtyInput = document.querySelector('.qty-val') || document.querySelector('.qty-input');
    const quantity = qtyInput ? (parseInt(qtyInput.value || qtyInput.textContent, 10) || 1) : 1;

    const originalText = addBtn.innerHTML;
    addBtn.disabled = true;
    addBtn.innerHTML = '<span>⏳ Adding...</span>';

    const formData = new FormData();
    formData.append('action', 'dmc_add_to_cart');
    formData.append('nonce', cartNonce);
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          updateHeaderCartBadge(data.data.item_count);
          addBtn.innerHTML = '<span>✓ Added to Cart!</span>';
          setTimeout(function () {
            addBtn.innerHTML = originalText;
            addBtn.disabled = false;
          }, 1800);
        } else {
          alert(data.data.message || 'Error adding product to cart.');
          addBtn.innerHTML = originalText;
          addBtn.disabled = false;
        }
      })
      .catch(function (err) {
        console.error('DMC Add to cart error:', err);
        addBtn.innerHTML = originalText;
        addBtn.disabled = false;
      });
  });

  // 2. Buy Now Button (Direct add and jump to checkout)
  document.addEventListener('click', function (e) {
    const buyBtn = e.target.closest('.dmc-buy-now-btn');
    if (!buyBtn) return;

    e.preventDefault();
    const productId = buyBtn.getAttribute('data-product-id');
    if (!productId) return;

    const qtyInput = document.querySelector('.qty-val') || document.querySelector('.qty-input');
    const quantity = qtyInput ? (parseInt(qtyInput.value || qtyInput.textContent, 10) || 1) : 1;

    buyBtn.disabled = true;
    buyBtn.innerHTML = '<span>⏳ Processing...</span>';

    const formData = new FormData();
    formData.append('action', 'dmc_add_to_cart');
    formData.append('nonce', cartNonce);
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          window.location.href = checkoutUrl;
        } else {
          alert(data.data.message || 'Error preparing checkout.');
          buyBtn.disabled = false;
        }
      })
      .catch(function () {
        window.location.href = checkoutUrl;
      });
  });

  // 3. Cart Page: AJAX Quantity Steppers
  document.addEventListener('click', function (e) {
    const minusBtn = e.target.closest('.dmc-qty-minus');
    const plusBtn  = e.target.closest('.dmc-qty-plus');

    if (!minusBtn && !plusBtn) return;

    e.preventDefault();
    const row = (minusBtn || plusBtn).closest('.dmc-cart-row');
    if (!row) return;

    const productId = row.getAttribute('data-product-id');
    const qtyValEl = row.querySelector('.dmc-qty-val');
    let currentQty = parseInt(qtyValEl.textContent, 10) || 1;

    if (minusBtn) {
      if (currentQty > 1) {
        currentQty -= 1;
      } else {
        return;
      }
    } else if (plusBtn) {
      currentQty += 1;
    }

    qtyValEl.textContent = currentQty;

    // Send AJAX update
    const formData = new FormData();
    formData.append('action', 'dmc_update_cart_quantity');
    formData.append('nonce', cartNonce);
    formData.append('product_id', productId);
    formData.append('quantity', currentQty);

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          const unitPrice = parseFloat(row.getAttribute('data-price')) || 0;
          const subtotalEl = row.querySelector('.dmc-row-subtotal');
          if (subtotalEl) {
            subtotalEl.textContent = '$' + (unitPrice * currentQty).toFixed(2);
          }
          updateCartSummary(data.data);
          updateHeaderCartBadge(data.data.item_count);
        }
      })
      .catch(function (err) {
        console.error('DMC quantity update error:', err);
      });
  });

  // 4. Cart Page: AJAX Remove Item
  document.addEventListener('click', function (e) {
    const removeBtn = e.target.closest('.dmc-cart-remove-btn');
    if (!removeBtn) return;

    e.preventDefault();
    const productId = removeBtn.getAttribute('data-product-id');
    const row = removeBtn.closest('.dmc-cart-row');

    if (!productId || !row) return;

    removeBtn.textContent = 'Removing...';

    const formData = new FormData();
    formData.append('action', 'dmc_remove_from_cart');
    formData.append('nonce', cartNonce);
    formData.append('product_id', productId);

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          row.style.opacity = '0.3';
          setTimeout(function () {
            row.remove();
            if (data.data.is_empty) {
              window.location.reload();
            } else {
              updateCartSummary(data.data);
              updateHeaderCartBadge(data.data.item_count);
            }
          }, 250);
        }
      })
      .catch(function (err) {
        console.error('DMC remove item error:', err);
        removeBtn.textContent = '✕ Remove';
      });
  });

  function updateCartSummary(data) {
    const subtotalEl = document.getElementById('cart-display-subtotal');
    const taxEl      = document.getElementById('cart-display-tax');
    const totalEl    = document.getElementById('cart-display-total');

    if (subtotalEl) subtotalEl.textContent = '$' + data.subtotal;
    if (taxEl) taxEl.textContent = '$' + data.tax;
    if (totalEl) totalEl.textContent = '$' + data.total;
  }
});
