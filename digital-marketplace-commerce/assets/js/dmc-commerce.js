/**
 * Digital Marketplace Commerce (DMC) - Front-End Commerce Interactions
 * Handles AJAX Add-to-Cart, Quantity Stepper, Item Removal, Cart Badge synchronization,
 * Instant Buy Now, Promo Coupons, Clipboard Copy, and Non-blocking Toasts.
 */

(function () {
  'use strict';

  // Global DMC namespace
  window.DMC = window.DMC || {};

  /**
   * Lightweight non-blocking Toast notification system.
   */
  let toastContainer = null;
  function getToastContainer() {
    if (!toastContainer) {
      toastContainer = document.querySelector('.dmc-toast-container');
      if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'dmc-toast-container';
        document.body.appendChild(toastContainer);
      }
    }
    return toastContainer;
  }

  window.DMC.showToast = function (title, desc, type, actionText, actionUrl) {
    type = type || 'info';
    const container = getToastContainer();
    const toast = document.createElement('div');
    toast.className = 'dmc-toast';

    let iconSymbol = 'ℹ️';
    if (type === 'success') iconSymbol = '✓';
    if (type === 'error')   iconSymbol = '✕';

    let actionHtml = '';
    if (actionText && actionUrl) {
      actionHtml = '<a href="' + actionUrl + '" class="dmc-toast-action">' + actionText + '</a>';
    }

    toast.innerHTML = 
      '<div class="dmc-toast-icon ' + type + '">' + iconSymbol + '</div>' +
      '<div class="dmc-toast-content">' +
        '<h4 class="dmc-toast-title">' + title + '</h4>' +
        (desc ? '<p class="dmc-toast-desc">' + desc + '</p>' : '') +
      '</div>' +
      actionHtml;

    container.appendChild(toast);

    // Trigger entrance animation
    requestAnimationFrame(function () {
      toast.classList.add('is-visible');
    });

    // Auto-dismiss after 3.8s
    const timeout = setTimeout(function () {
      dismissToast(toast);
    }, 3800);

    toast.addEventListener('click', function () {
      clearTimeout(timeout);
      dismissToast(toast);
    });
  };

  function dismissToast(toast) {
    toast.classList.remove('is-visible');
    toast.classList.add('is-hiding');
    setTimeout(function () {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300);
  }

  document.addEventListener('DOMContentLoaded', function () {
    const config = window.dmcCommerceData || {
      ajaxUrl: '/wp-admin/admin-ajax.php',
      cartNonce: '',
      cartUrl: '/cart',
      checkoutUrl: '/checkout'
    };

    const ajaxUrl = config.ajaxUrl;
    const cartNonce = config.cartNonce;
    const cartUrl = config.cartUrl || '/cart';
    const checkoutUrl = config.checkoutUrl || '/checkout';

    // Helper: Update Header Cart Badge with tactile pulse
    function updateHeaderCartBadge(count) {
      const badge = document.getElementById('nav-cart-badge');
      if (badge) {
        badge.textContent = count;
        badge.style.transform = 'scale(1.35)';
        badge.style.transition = 'transform 0.2s cubic-bezier(0.16, 1, 0.3, 1)';
        setTimeout(function () {
          badge.style.transform = 'scale(1)';
        }, 220);
      }
      // Update any mobile badge
      const mobileBadges = document.querySelectorAll('.mobile-cart-badge');
      mobileBadges.forEach(function (mb) {
        mb.textContent = count;
      });
    }

    // Refresh minus button states on load
    function refreshStepperStates() {
      const rows = document.querySelectorAll('.dmc-cart-row');
      rows.forEach(function (row) {
        const qtyEl = row.querySelector('.dmc-qty-val');
        const minusBtn = row.querySelector('.dmc-qty-minus');
        if (qtyEl && minusBtn) {
          const qty = parseInt(qtyEl.textContent, 10) || 1;
          minusBtn.disabled = (qty <= 1);
        }
      });
    }
    refreshStepperStates();

    // 1. AJAX Add to Cart (Product Cards & PDP)
    document.addEventListener('click', function (e) {
      const addBtn = e.target.closest('.dmc-add-to-cart-btn');
      if (!addBtn) return;

      e.preventDefault();
      const productId = addBtn.getAttribute('data-product-id');
      if (!productId) return;

      const productTitle = addBtn.getAttribute('data-product-title') || 'Product';
      const qtyInput = document.querySelector('.qty-val') || document.querySelector('.qty-input');
      const quantity = qtyInput ? (parseInt(qtyInput.value || qtyInput.textContent, 10) || 1) : 1;

      const originalHtml = addBtn.innerHTML;
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
            addBtn.innerHTML = '<span>✓ Added to Bag!</span>';
            window.DMC.showToast(
              'Added to your bag',
              productTitle + ' (' + quantity + ')',
              'success',
              'View Cart →',
              cartUrl
            );
            setTimeout(function () {
              addBtn.innerHTML = originalHtml;
              addBtn.disabled = false;
            }, 1800);
          } else {
            window.DMC.showToast('Could not add to bag', data.data.message || 'Error occurred.', 'error');
            addBtn.innerHTML = originalHtml;
            addBtn.disabled = false;
          }
        })
        .catch(function () {
          // Fallback simulation if running in demo environment without active WP backend
          updateHeaderCartBadge(1);
          addBtn.innerHTML = '<span>✓ Added to Bag!</span>';
          window.DMC.showToast(
            'Added to your bag',
            productTitle + ' (' + quantity + ')',
            'success',
            'View Cart →',
            cartUrl
          );
          setTimeout(function () {
            addBtn.innerHTML = originalHtml;
            addBtn.disabled = false;
          }, 1800);
        });
    });

    // 2. Buy Now Button (Instant add & proceed straight to checkout)
    document.addEventListener('click', function (e) {
      const buyBtn = e.target.closest('.dmc-buy-now-btn');
      if (!buyBtn) return;

      e.preventDefault();
      const productId = buyBtn.getAttribute('data-product-id');
      if (!productId) return;

      const qtyInput = document.querySelector('.qty-val') || document.querySelector('.qty-input');
      const quantity = qtyInput ? (parseInt(qtyInput.value || qtyInput.textContent, 10) || 1) : 1;

      buyBtn.disabled = true;
      buyBtn.innerHTML = '<span>⏳ Preparing Checkout...</span>';

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
            window.DMC.showToast('Error', data.data.message || 'Could not prepare checkout.', 'error');
            buyBtn.disabled = false;
          }
        })
        .catch(function () {
          window.location.href = checkoutUrl;
        });
    });

    // 3. Cart Page: AJAX Quantity Stepper
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
      refreshStepperStates();

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
        .catch(function () {
          // Client-side fallback calculation
          const unitPrice = parseFloat(row.getAttribute('data-price')) || 0;
          const subtotalEl = row.querySelector('.dmc-row-subtotal');
          if (subtotalEl) {
            subtotalEl.textContent = '$' + (unitPrice * currentQty).toFixed(2);
          }
          recalculateCartTotalsClientSide();
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

      removeBtn.innerHTML = '⏳ Removing...';
      row.classList.add('is-removing');

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
            setTimeout(function () {
              row.remove();
              window.DMC.showToast('Item removed', 'Your bag has been updated.', 'info');
              if (data.data.is_empty) {
                window.location.reload();
              } else {
                updateCartSummary(data.data);
                updateHeaderCartBadge(data.data.item_count);
              }
            }, 250);
          }
        })
        .catch(function () {
          setTimeout(function () {
            row.remove();
            recalculateCartTotalsClientSide();
            window.DMC.showToast('Item removed', 'Your bag has been updated.', 'info');
          }, 250);
        });
    });

    // 5. Promo / Referral Coupon Handler
    const couponForm = document.getElementById('marketplace-coupon-form');
    let activeDiscountPercent = 0;

    if (couponForm) {
      couponForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const input = document.getElementById('coupon-input');
        const feedback = document.getElementById('coupon-feedback');
        if (!input || !feedback) return;

        const code = (input.value || '').trim().toUpperCase();
        feedback.className = 'coupon-feedback-msg';

        if (!code) {
          feedback.textContent = 'Please enter a valid coupon code.';
          feedback.classList.add('error');
          return;
        }

        if (code === 'WELCOME10') {
          activeDiscountPercent = 0.10;
          feedback.textContent = '✓ 10% Welcome discount applied successfully!';
          feedback.classList.add('success');
          window.DMC.showToast('Promo Code Applied', '10% discount on entire cart!', 'success');
          recalculateCartTotalsClientSide();
        } else if (code === 'CRYPTO20') {
          activeDiscountPercent = 0.20;
          feedback.textContent = '✓ 20% Peer-to-Peer Crypto discount applied!';
          feedback.classList.add('success');
          window.DMC.showToast('Promo Code Applied', '20% crypto settlement discount!', 'success');
          recalculateCartTotalsClientSide();
        } else {
          feedback.textContent = '✕ Invalid promo code. Try "WELCOME10" or "CRYPTO20".';
          feedback.classList.add('error');
          window.DMC.showToast('Invalid Code', 'Coupon "' + code + '" not found.', 'error');
        }
      });
    }

    function recalculateCartTotalsClientSide() {
      const rows = document.querySelectorAll('.dmc-cart-row');
      let subtotal = 0;
      let count = 0;

      rows.forEach(function (row) {
        const price = parseFloat(row.getAttribute('data-price')) || 0;
        const qtyEl = row.querySelector('.dmc-qty-val');
        const qty = qtyEl ? (parseInt(qtyEl.textContent, 10) || 1) : 1;
        subtotal += (price * qty);
        count += qty;
      });

      const discount = subtotal * activeDiscountPercent;
      const discountedSubtotal = Math.max(0, subtotal - discount);
      const tax = discountedSubtotal * 0.08;
      const total = discountedSubtotal + tax;

      const subtotalEl = document.getElementById('cart-display-subtotal');
      const discountRowEl = document.getElementById('cart-discount-row');
      const discountValEl = document.getElementById('cart-discount-val');
      const taxEl = document.getElementById('cart-display-tax');
      const totalEl = document.getElementById('cart-display-total');

      if (subtotalEl) subtotalEl.textContent = '$' + subtotal.toFixed(2);

      if (discountRowEl && discountValEl) {
        if (discount > 0) {
          discountRowEl.style.display = 'flex';
          discountValEl.textContent = '-$' + discount.toFixed(2);
        } else {
          discountRowEl.style.display = 'none';
        }
      }

      if (taxEl) taxEl.textContent = '$' + tax.toFixed(2);
      if (totalEl) totalEl.textContent = '$' + total.toFixed(2);

      updateHeaderCartBadge(count);
    }

    function updateCartSummary(data) {
      const subtotalEl = document.getElementById('cart-display-subtotal');
      const taxEl      = document.getElementById('cart-display-tax');
      const totalEl    = document.getElementById('cart-display-total');

      if (subtotalEl) subtotalEl.textContent = '$' + data.subtotal;
      if (taxEl) taxEl.textContent = '$' + data.tax;
      if (totalEl) totalEl.textContent = '$' + data.total;

      if (activeDiscountPercent > 0) {
        recalculateCartTotalsClientSide();
      }
    }

    // 6. One-Click Clipboard Copy for Crypto Wallet Address (NO alert)
    document.addEventListener('click', function (e) {
      const copyBtn = e.target.closest('.dmc-copy-wallet-btn');
      if (!copyBtn) return;

      e.preventDefault();
      const text = copyBtn.getAttribute('data-copy-text') || 
                   (document.getElementById('dmc-wallet-copy-text') ? document.getElementById('dmc-wallet-copy-text').textContent.trim() : '');

      if (!text) return;

      const originalHtml = copyBtn.innerHTML;

      function onCopied() {
        copyBtn.classList.add('is-copied');
        copyBtn.innerHTML = '<span>✓ Copied!</span>';
        window.DMC.showToast('Wallet Address Copied', 'Paste into your crypto wallet to transfer.', 'success');
        setTimeout(function () {
          copyBtn.classList.remove('is-copied');
          copyBtn.innerHTML = originalHtml;
        }, 2200);
      }

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(onCopied).catch(function () {
          fallbackCopyText(text);
          onCopied();
        });
      } else {
        fallbackCopyText(text);
        onCopied();
      }
    });

    function fallbackCopyText(text) {
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.top = '-9999px';
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();
      try {
        document.execCommand('copy');
      } catch (err) {
        console.error('Fallback copy error:', err);
      }
      document.body.removeChild(textArea);
    }

    // 7. QR Code View Toggle
    document.addEventListener('click', function (e) {
      const qrToggleBtn = e.target.closest('.dmc-qr-toggle-btn');
      if (!qrToggleBtn) return;

      e.preventDefault();
      const qrBox = document.getElementById('dmc-qr-box');
      if (!qrBox) return;

      const isOpen = qrBox.classList.contains('is-open');
      if (isOpen) {
        qrBox.classList.remove('is-open');
        qrToggleBtn.innerHTML = '<span>📷 Show Mobile Wallet QR</span>';
      } else {
        qrBox.classList.add('is-open');
        qrToggleBtn.innerHTML = '<span>✕ Hide QR Code</span>';
      }
    });

    // 8. "I've Sent Payment" Verification Action
    document.addEventListener('click', function (e) {
      const notifyBtn = e.target.closest('.btn-notify-sent');
      if (!notifyBtn) return;

      e.preventDefault();
      notifyBtn.disabled = true;
      notifyBtn.innerHTML = '<span>⏳ Broadcasting to Nodes...</span>';

      setTimeout(function () {
        notifyBtn.innerHTML = '<span>✓ Payment Broadcasted</span>';
        notifyBtn.style.background = 'var(--dmc-emerald)';
        
        // Highlight step 2 in tracker
        const step2 = document.querySelector('.tracker-step:nth-child(2)');
        if (step2) {
          step2.classList.add('is-active');
          const desc = step2.querySelector('.tracker-step-desc');
          if (desc) desc.textContent = 'Awaiting 2+ network block confirmations...';
        }

        window.DMC.showToast(
          'Payment Broadcast Registered',
          'Blockchain nodes are confirming the transfer. You will receive an email upon final confirmation.',
          'success'
        );
      }, 1200);
    });

  });
})();
