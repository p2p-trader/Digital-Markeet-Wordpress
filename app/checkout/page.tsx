'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import {
  ShieldCheck,
  CreditCard,
  Lock,
  CheckCircle,
  Download,
  ArrowRight,
  ShoppingBag,
  ExternalLink,
} from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';
import { Order } from '@/types/marketplace';

export default function CheckoutPage() {
  const { cart, cartSubtotal, cartTax, cartTotal, placeOrder, currentUser } = useMarketplace();

  // Form State
  const [fullName, setFullName] = useState(currentUser?.name || '');
  const [email, setEmail] = useState(currentUser?.email || '');
  const [address, setAddress] = useState('128 Innovation Way');
  const [city, setCity] = useState('San Francisco');
  const [zipCode, setZipCode] = useState('94105');
  const [country, setCountry] = useState('United States');
  
  // Payment placeholder states
  const [paymentMethod, setPaymentMethod] = useState<'card' | 'paypal'>('card');
  const [cardNumber, setCardNumber] = useState('•••• •••• •••• 4242');
  const [cardExpiry, setCardExpiry] = useState('12/28');
  const [cardCvc, setCardCvc] = useState('321');

  // UI / Status states
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [completedOrder, setCompletedOrder] = useState<Order | null>(null);

  const validateForm = () => {
    const errs: Record<string, string> = {};
    if (!fullName.trim()) errs.fullName = 'Full name is required';
    if (!email.trim()) {
      errs.email = 'Email address is required';
    } else if (!/\S+@\S+\.\S+/.test(email)) {
      errs.email = 'Please enter a valid email address';
    }
    if (!address.trim()) errs.address = 'Street address is required';
    if (!city.trim()) errs.city = 'City is required';
    if (!zipCode.trim()) errs.zipCode = 'Postal / ZIP code is required';

    if (paymentMethod === 'card') {
      if (!cardNumber.trim()) errs.cardNumber = 'Card number placeholder required';
      if (!cardExpiry.trim()) errs.cardExpiry = 'Expiry date required';
      if (!cardCvc.trim()) errs.cardCvc = 'CVC required';
    }

    setErrors(errs);
    return Object.keys(errs).length === 0;
  };

  const handleCheckoutSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!validateForm()) return;

    setIsSubmitting(true);

    // Simulate instant transaction process
    setTimeout(() => {
      const order = placeOrder({
        fullName: fullName.trim(),
        email: email.trim(),
        address: address.trim(),
        city: city.trim(),
        zipCode: zipCode.trim(),
        paymentMethod:
          paymentMethod === 'card'
            ? 'Credit Card (ending in 4242)'
            : 'PayPal Account',
      });

      setCompletedOrder(order);
      setIsSubmitting(false);
    }, 900);
  };

  // If order was just placed, display Success screen
  if (completedOrder) {
    return (
      <div id="checkout-success-view" className="max-w-3xl mx-auto px-4 sm:px-6 py-12 w-full">
        <div className="bg-white rounded-3xl border border-stone-200 p-8 sm:p-12 shadow-sm text-center">
          
          <div className="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-5">
            <CheckCircle className="w-9 h-9" />
          </div>

          <span className="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
            Payment Confirmed
          </span>

          <h1 className="text-2xl sm:text-3xl font-extrabold text-stone-900 mt-3 tracking-tight">
            Thank you for your purchase!
          </h1>
          <p className="text-sm text-stone-500 mt-2 max-w-md mx-auto">
            Order <strong className="text-stone-800">{completedOrder.id}</strong> has been processed. A receipt and download links have been sent to <span className="text-stone-800 font-semibold">{completedOrder.customer.email}</span>.
          </p>

          {/* Purchased Items Download Links */}
          <div className="mt-8 text-left border-t border-stone-100 pt-6">
            <h3 className="text-xs font-bold uppercase tracking-wider text-stone-400 mb-3">
              Your Purchased Downloads ({completedOrder.items.length})
            </h3>

            <div className="space-y-3">
              {completedOrder.items.map((item, idx) => (
                <div
                  key={idx}
                  className="flex items-center justify-between p-3.5 bg-stone-50 rounded-xl border border-stone-200"
                >
                  <div className="flex items-center gap-3 min-w-0">
                    <div className="relative w-12 h-12 rounded-lg overflow-hidden bg-stone-200 shrink-0">
                      <Image
                        src={item.image}
                        alt={item.title}
                        fill
                        sizes="48px"
                        className="object-cover"
                        referrerPolicy="no-referrer"
                      />
                    </div>
                    <div className="min-w-0">
                      <h4 className="text-xs sm:text-sm font-bold text-stone-900 truncate">
                        {item.title}
                      </h4>
                      <p className="text-[11px] text-stone-500">
                        Qty: {item.quantity} • Commercial License
                      </p>
                    </div>
                  </div>

                  <a
                    href="#download"
                    onClick={(e) => {
                      e.preventDefault();
                      alert(`Initiating verified download package for "${item.title}"`);
                    }}
                    className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg shrink-0 transition"
                  >
                    <Download className="w-3.5 h-3.5 text-amber-300" />
                    <span>Download</span>
                  </a>
                </div>
              ))}
            </div>
          </div>

          {/* Navigation Action Buttons */}
          <div className="mt-8 pt-6 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-center gap-3">
            <Link
              id="order-success-account-btn"
              href="/account"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-stone-900 hover:bg-stone-800 text-white text-xs sm:text-sm font-semibold rounded-xl transition"
            >
              <span>View in Account History</span>
              <ArrowRight className="w-4 h-4" />
            </Link>

            <Link
              href="/products"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs sm:text-sm font-semibold rounded-xl transition"
            >
              <span>Explore More Products</span>
            </Link>
          </div>

        </div>
      </div>
    );
  }

  // If cart is empty and no order completed yet
  if (cart.length === 0) {
    return (
      <div className="max-w-2xl mx-auto px-4 py-16 text-center">
        <div className="w-14 h-14 rounded-full bg-stone-100 flex items-center justify-center text-stone-400 mx-auto mb-4">
          <ShoppingBag className="w-6 h-6" />
        </div>
        <h2 className="text-xl font-bold text-stone-900">Your cart is empty</h2>
        <p className="text-sm text-stone-500 mt-2">
          Please add items to your cart before proceeding to checkout.
        </p>
        <Link
          href="/products"
          className="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-stone-900 text-white text-xs font-semibold rounded-xl hover:bg-stone-800 transition"
        >
          <span>Browse Marketplace</span>
          <ArrowRight className="w-4 h-4" />
        </Link>
      </div>
    );
  }

  return (
    <div id="checkout-page-container" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex flex-col gap-8">
      
      {/* Page Title */}
      <div className="border-b border-stone-200 pb-5">
        <h1 className="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
          Checkout
        </h1>
        <p className="text-xs sm:text-sm text-stone-500 mt-1">
          Complete your customer information and placeholder payment details.
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
        
        {/* Left Column: Form */}
        <div className="lg:col-span-7">
          <form id="checkout-form" onSubmit={handleCheckoutSubmit} className="space-y-8">
            
            {/* Contact Information */}
            <div className="bg-white rounded-2xl border border-stone-200 p-6 shadow-2xs space-y-4">
              <h2 className="text-base font-bold text-stone-900">
                1. Customer & Delivery Information
              </h2>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="sm:col-span-2">
                  <label htmlFor="checkout-name" className="block text-xs font-semibold text-stone-700 mb-1">
                    Full Name *
                  </label>
                  <input
                    id="checkout-name"
                    type="text"
                    value={fullName}
                    onChange={(e) => setFullName(e.target.value)}
                    placeholder="e.g. Syed Bakhtawar Shah"
                    className={`w-full px-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                      errors.fullName ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                    }`}
                  />
                  {errors.fullName && (
                    <p className="text-xs text-rose-600 mt-1">{errors.fullName}</p>
                  )}
                </div>

                <div className="sm:col-span-2">
                  <label htmlFor="checkout-email" className="block text-xs font-semibold text-stone-700 mb-1">
                    Email Address (for download delivery) *
                  </label>
                  <input
                    id="checkout-email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="e.g. user@example.com"
                    className={`w-full px-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                      errors.email ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                    }`}
                  />
                  {errors.email && (
                    <p className="text-xs text-rose-600 mt-1">{errors.email}</p>
                  )}
                </div>

                <div className="sm:col-span-2">
                  <label htmlFor="checkout-address" className="block text-xs font-semibold text-stone-700 mb-1">
                    Street Address / Company *
                  </label>
                  <input
                    id="checkout-address"
                    type="text"
                    value={address}
                    onChange={(e) => setAddress(e.target.value)}
                    placeholder="128 Innovation Way"
                    className={`w-full px-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                      errors.address ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                    }`}
                  />
                  {errors.address && (
                    <p className="text-xs text-rose-600 mt-1">{errors.address}</p>
                  )}
                </div>

                <div>
                  <label htmlFor="checkout-city" className="block text-xs font-semibold text-stone-700 mb-1">
                    City *
                  </label>
                  <input
                    id="checkout-city"
                    type="text"
                    value={city}
                    onChange={(e) => setCity(e.target.value)}
                    placeholder="San Francisco"
                    className={`w-full px-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                      errors.city ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                    }`}
                  />
                  {errors.city && (
                    <p className="text-xs text-rose-600 mt-1">{errors.city}</p>
                  )}
                </div>

                <div>
                  <label htmlFor="checkout-zip" className="block text-xs font-semibold text-stone-700 mb-1">
                    Postal / ZIP Code *
                  </label>
                  <input
                    id="checkout-zip"
                    type="text"
                    value={zipCode}
                    onChange={(e) => setZipCode(e.target.value)}
                    placeholder="94105"
                    className={`w-full px-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                      errors.zipCode ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                    }`}
                  />
                  {errors.zipCode && (
                    <p className="text-xs text-rose-600 mt-1">{errors.zipCode}</p>
                  )}
                </div>
              </div>
            </div>

            {/* Payment Information Placeholder */}
            <div className="bg-white rounded-2xl border border-stone-200 p-6 shadow-2xs space-y-4">
              <div className="flex items-center justify-between">
                <h2 className="text-base font-bold text-stone-900">
                  2. Payment Method (UI Placeholder)
                </h2>
                <div className="flex items-center gap-1 text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                  <Lock className="w-3 h-3" />
                  <span>Sandbox Demo Mode</span>
                </div>
              </div>

              {/* Payment selector tabs */}
              <div className="flex gap-3">
                <button
                  type="button"
                  onClick={() => setPaymentMethod('card')}
                  className={`flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border text-xs font-semibold transition cursor-pointer ${
                    paymentMethod === 'card'
                      ? 'border-stone-900 bg-stone-900 text-white shadow-2xs'
                      : 'border-stone-200 bg-stone-50 text-stone-600 hover:bg-stone-100'
                  }`}
                >
                  <CreditCard className="w-4 h-4" />
                  <span>Credit / Debit Card</span>
                </button>

                <button
                  type="button"
                  onClick={() => setPaymentMethod('paypal')}
                  className={`flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border text-xs font-semibold transition cursor-pointer ${
                    paymentMethod === 'paypal'
                      ? 'border-stone-900 bg-stone-900 text-white shadow-2xs'
                      : 'border-stone-200 bg-stone-50 text-stone-600 hover:bg-stone-100'
                  }`}
                >
                  <span>PayPal (Simulated)</span>
                </button>
              </div>

              {paymentMethod === 'card' ? (
                <div className="space-y-3 pt-2">
                  <div>
                    <label htmlFor="checkout-card-number" className="block text-xs font-semibold text-stone-700 mb-1">
                      Card Number Placeholder
                    </label>
                    <input
                      id="checkout-card-number"
                      type="text"
                      value={cardNumber}
                      onChange={(e) => setCardNumber(e.target.value)}
                      className="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-mono focus:outline-none focus:bg-white focus:border-stone-400 transition"
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-3">
                    <div>
                      <label htmlFor="checkout-card-exp" className="block text-xs font-semibold text-stone-700 mb-1">
                        Expiry (MM/YY)
                      </label>
                      <input
                        id="checkout-card-exp"
                        type="text"
                        value={cardExpiry}
                        onChange={(e) => setCardExpiry(e.target.value)}
                        className="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-mono focus:outline-none focus:bg-white focus:border-stone-400 transition"
                      />
                    </div>
                    <div>
                      <label htmlFor="checkout-card-cvc" className="block text-xs font-semibold text-stone-700 mb-1">
                        CVC Security Code
                      </label>
                      <input
                        id="checkout-card-cvc"
                        type="text"
                        value={cardCvc}
                        onChange={(e) => setCardCvc(e.target.value)}
                        className="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-mono focus:outline-none focus:bg-white focus:border-stone-400 transition"
                      />
                    </div>
                  </div>
                </div>
              ) : (
                <div className="p-4 bg-stone-50 rounded-xl border border-stone-200 text-xs text-stone-600 text-center">
                  You will be securely redirected to PayPal sandbox to authorize purchase.
                </div>
              )}

              <p className="text-[11px] text-stone-400 leading-relaxed pt-2">
                This is a mock UI checkout as requested. No real credit card or bank account will be charged.
              </p>
            </div>

            {/* Submit Button */}
            <button
              id="checkout-submit-btn"
              type="submit"
              disabled={isSubmitting}
              className="w-full flex items-center justify-center gap-2 py-4 px-6 bg-stone-900 hover:bg-stone-800 active:bg-stone-950 disabled:opacity-50 text-white font-bold rounded-2xl shadow-sm transition text-base cursor-pointer"
            >
              {isSubmitting ? (
                <span>Authorizing Order...</span>
              ) : (
                <>
                  <Lock className="w-4 h-4 text-amber-300" />
                  <span>Complete Purchase (${cartTotal.toFixed(2)})</span>
                </>
              )}
            </button>

          </form>
        </div>

        {/* Right Column: Order Summary Box */}
        <div className="lg:col-span-5">
          <div className="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-5 sticky top-24">
            <h3 className="text-base font-bold text-stone-900 border-b border-stone-100 pb-3">
              Order Summary ({cart.length} items)
            </h3>

            {/* Items list preview */}
            <div className="space-y-3 max-h-72 overflow-y-auto pr-1">
              {cart.map((item) => (
                <div key={item.product.id} className="flex items-center gap-3">
                  <div className="relative w-12 h-12 rounded-lg overflow-hidden bg-stone-100 shrink-0 border border-stone-100">
                    <Image
                      src={item.product.image}
                      alt={item.product.title}
                      fill
                      sizes="48px"
                      className="object-cover"
                      referrerPolicy="no-referrer"
                    />
                  </div>
                  <div className="flex-1 min-w-0">
                    <h4 className="text-xs font-bold text-stone-900 truncate">
                      {item.product.title}
                    </h4>
                    <p className="text-[11px] text-stone-500">
                      Qty: {item.quantity} × ${item.product.price}
                    </p>
                  </div>
                  <span className="text-xs font-bold text-stone-900">
                    ${(item.product.price * item.quantity).toFixed(2)}
                  </span>
                </div>
              ))}
            </div>

            {/* Subtotal & Calculations */}
            <div className="border-t border-stone-100 pt-4 space-y-2 text-xs text-stone-600">
              <div className="flex justify-between">
                <span>Subtotal</span>
                <span className="font-semibold text-stone-900">${cartSubtotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between">
                <span>Tax (8%)</span>
                <span className="font-semibold text-stone-900">${cartTax.toFixed(2)}</span>
              </div>
              <div className="border-t border-stone-200 pt-3 flex justify-between items-baseline">
                <span className="text-sm font-extrabold text-stone-900">Total Due</span>
                <span className="text-xl font-black text-stone-900">
                  ${cartTotal.toFixed(2)}
                </span>
              </div>
            </div>

            {/* Trust Points */}
            <div className="p-3.5 bg-stone-50 rounded-xl border border-stone-200 text-[11px] text-stone-600 space-y-1.5">
              <div className="flex items-center gap-2 font-medium text-stone-800">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                <span>256-Bit SSL Mock Security Protocol</span>
              </div>
              <p className="text-stone-500 leading-relaxed">
                Files are decrypted and ready for download immediately upon order completion.
              </p>
            </div>

          </div>
        </div>

      </div>

    </div>
  );
}
