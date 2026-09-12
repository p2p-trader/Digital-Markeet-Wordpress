'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import {
  ShoppingBag,
  ArrowRight,
  ArrowLeft,
  Trash2,
  ShieldCheck,
  Tag,
  Check,
} from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';
import { CartItem } from '@/components/CartItem';

export default function CartPage() {
  const router = useRouter();
  const { cart, clearCart, cartCount, cartSubtotal, cartTax, cartTotal, showToast } = useMarketplace();

  const [couponCode, setCouponCode] = useState('');
  const [discountAmount, setDiscountAmount] = useState(0);
  const [couponApplied, setCouponApplied] = useState(false);

  const handleApplyCoupon = (e: React.FormEvent) => {
    e.preventDefault();
    const code = couponCode.trim().toUpperCase();
    if (code === 'WELCOME10' || code === 'SAVE10') {
      const discount = Number((cartSubtotal * 0.1).toFixed(2));
      setDiscountAmount(discount);
      setCouponApplied(true);
      showToast('10% discount promo applied successfully!');
    } else if (code === 'CREATOR20') {
      const discount = Number((cartSubtotal * 0.2).toFixed(2));
      setDiscountAmount(discount);
      setCouponApplied(true);
      showToast('20% creator discount applied!');
    } else if (code) {
      showToast('Invalid promo code. Try "WELCOME10" or "CREATOR20"');
    }
  };

  const finalTotal = Math.max(0, Number((cartTotal - discountAmount).toFixed(2)));

  return (
    <div id="cart-page-container" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex flex-col gap-8">
      
      {/* Page Title */}
      <div className="flex items-center justify-between border-b border-stone-200 pb-5">
        <div>
          <h1 className="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
            Shopping Cart
          </h1>
          <p className="text-xs sm:text-sm text-stone-500 mt-1">
            Review your selected digital licenses before proceeding to checkout.
          </p>
        </div>

        {cart.length > 0 && (
          <button
            id="clear-cart-btn"
            type="button"
            onClick={clearCart}
            className="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-500 hover:text-rose-600 transition cursor-pointer"
          >
            <Trash2 className="w-3.5 h-3.5" />
            <span>Clear Cart</span>
          </button>
        )}
      </div>

      {cart.length > 0 ? (
        /* Active Cart Layout */
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
          
          {/* Cart Items List */}
          <div className="lg:col-span-8 space-y-4">
            <div className="flex items-center justify-between text-xs font-semibold text-stone-500 px-1">
              <span>{cartCount} {cartCount === 1 ? 'Product' : 'Products'} in Cart</span>
              <Link href="/products" className="text-stone-900 hover:underline inline-flex items-center gap-1">
                <ArrowLeft className="w-3.5 h-3.5" />
                <span>Continue Browsing</span>
              </Link>
            </div>

            <div id="cart-items-list" className="space-y-3">
              {cart.map((item) => (
                <CartItem key={item.product.id} item={item} />
              ))}
            </div>

            {/* Promo Code Box */}
            <div className="bg-white rounded-xl border border-stone-200 p-4 sm:p-5 shadow-2xs">
              <div className="flex items-center justify-between mb-2">
                <span className="text-xs font-bold text-stone-800 flex items-center gap-1.5">
                  <Tag className="w-3.5 h-3.5 text-stone-500" />
                  Have a Promo / Discount Code?
                </span>
                <span className="text-[11px] text-stone-400">Use code: <strong className="text-stone-700">WELCOME10</strong></span>
              </div>

              <form onSubmit={handleApplyCoupon} className="flex gap-2">
                <input
                  id="cart-coupon-input"
                  type="text"
                  placeholder="Enter coupon code"
                  value={couponCode}
                  onChange={(e) => setCouponCode(e.target.value)}
                  className="flex-1 px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs uppercase font-medium placeholder-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-900"
                />
                <button
                  type="submit"
                  className="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg transition cursor-pointer"
                >
                  Apply
                </button>
              </form>

              {couponApplied && (
                <div className="mt-2 text-xs text-emerald-700 flex items-center gap-1 font-medium">
                  <Check className="w-3.5 h-3.5" />
                  Coupon code applied (-${discountAmount.toFixed(2)})
                </div>
              )}
            </div>

          </div>

          {/* Cart Summary Card */}
          <div className="lg:col-span-4">
            <div
              id="cart-order-summary"
              className="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-5 sticky top-24"
            >
              <h3 className="text-base font-bold text-stone-900 border-b border-stone-100 pb-3">
                Order Summary
              </h3>

              <div className="space-y-3 text-xs sm:text-sm">
                <div className="flex justify-between text-stone-600">
                  <span>Subtotal ({cartCount} items)</span>
                  <span className="font-semibold text-stone-900">${cartSubtotal.toFixed(2)}</span>
                </div>

                <div className="flex justify-between text-stone-600">
                  <span>Estimated Tax (8%)</span>
                  <span className="font-semibold text-stone-900">${cartTax.toFixed(2)}</span>
                </div>

                {discountAmount > 0 && (
                  <div className="flex justify-between text-emerald-600 font-semibold">
                    <span>Discount</span>
                    <span>-${discountAmount.toFixed(2)}</span>
                  </div>
                )}

                <div className="border-t border-stone-200 pt-3 flex justify-between items-baseline">
                  <span className="text-base font-extrabold text-stone-900">Total</span>
                  <span className="text-2xl font-black text-stone-900">
                    ${finalTotal.toFixed(2)}
                  </span>
                </div>
              </div>

              {/* Checkout Button */}
              <Link
                id="cart-checkout-btn"
                href="/checkout"
                className="w-full flex items-center justify-center gap-2 py-3.5 px-6 bg-stone-900 hover:bg-stone-800 active:bg-stone-950 text-white font-bold rounded-xl shadow-xs transition text-sm text-center"
              >
                <span>Proceed to Checkout</span>
                <ArrowRight className="w-4 h-4" />
              </Link>

              {/* Trust & Guarantee points */}
              <div className="pt-3 border-t border-stone-100 space-y-2 text-[11px] text-stone-500">
                <div className="flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4 text-emerald-600 shrink-0" />
                  <span>Instant access link sent to your email</span>
                </div>
                <div className="flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4 text-emerald-600 shrink-0" />
                  <span>14-day creator money-back guarantee</span>
                </div>
              </div>

            </div>
          </div>

        </div>
      ) : (
        /* Empty Cart State */
        <div id="cart-empty-state" className="flex flex-col items-center justify-center p-12 sm:p-16 bg-white rounded-2xl border border-stone-200 text-center my-6">
          <div className="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center text-stone-400 mb-4">
            <ShoppingBag className="w-8 h-8" />
          </div>
          <h2 className="text-xl font-bold text-stone-900">Your cart is empty</h2>
          <p className="text-sm text-stone-500 mt-1 max-w-sm">
            Looks like you have not added any digital items or boilerplates to your cart yet.
          </p>
          <Link
            id="empty-cart-browse-btn"
            href="/products"
            className="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-stone-900 text-white text-xs sm:text-sm font-semibold rounded-xl hover:bg-stone-800 transition shadow-xs"
          >
            <span>Explore Marketplace Catalog</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      )}

    </div>
  );
}
