'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import {
  User,
  ShoppingBag,
  DownloadCloud,
  LogOut,
  Calendar,
  CheckCircle,
  ExternalLink,
  PackageCheck,
  ShieldCheck,
  FileCode,
  ArrowRight,
} from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';

export default function AccountPage() {
  const { currentUser, orders, logout, showToast } = useMarketplace();
  const [activeTab, setActiveTab] = useState<'orders' | 'downloads' | 'settings'>('orders');

  if (!currentUser) {
    return (
      <div id="account-guest-view" className="max-w-md mx-auto px-4 py-20 text-center flex-1 flex flex-col justify-center">
        <div className="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center text-stone-400 mx-auto mb-4">
          <User className="w-8 h-8" />
        </div>
        <h1 className="text-2xl font-bold text-stone-900">Sign in to your Account</h1>
        <p className="text-sm text-stone-500 mt-2">
          Please sign in to view your order history, access instant downloads, and manage licenses.
        </p>
        <div className="mt-6 flex flex-col gap-3">
          <Link
            href="/login"
            className="w-full py-3 px-4 bg-stone-900 hover:bg-stone-800 text-white text-xs sm:text-sm font-semibold rounded-xl transition"
          >
            Sign In / Register
          </Link>
          <Link
            href="/products"
            className="w-full py-2.5 px-4 bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-semibold rounded-xl transition"
          >
            Browse Products First
          </Link>
        </div>
      </div>
    );
  }

  // Consolidated list of all purchased items across all orders
  const allPurchasedItems = orders.flatMap((ord) =>
    ord.items.map((item) => ({
      ...item,
      orderId: ord.id,
      orderDate: ord.createdAt,
    }))
  );

  const handleDownload = (title: string) => {
    showToast(`Downloading package for "${title}"...`);
  };

  return (
    <div id="account-dashboard" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex flex-col gap-8">
      
      {/* Profile Overview Header */}
      <div className="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div className="flex items-center gap-4">
          <div className="relative w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-stone-200 border-2 border-stone-100 shrink-0">
            <Image
              src={currentUser.avatarUrl}
              alt={currentUser.name}
              fill
              sizes="80px"
              className="object-cover"
              referrerPolicy="no-referrer"
            />
          </div>
          <div>
            <div className="flex items-center gap-2">
              <h1 className="text-xl sm:text-2xl font-black text-stone-900">
                {currentUser.name}
              </h1>
              <span className="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-900 rounded-md">
                Verified Customer
              </span>
            </div>
            <p className="text-xs sm:text-sm text-stone-500 mt-0.5">{currentUser.email}</p>
            <p className="text-xs text-stone-400 mt-1 flex items-center gap-1.5">
              <Calendar className="w-3.5 h-3.5" /> Member since {currentUser.joinedDate}
            </p>
          </div>
        </div>

        {/* Quick Stats & Sign Out */}
        <div className="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end pt-4 md:pt-0 border-t md:border-t-0 border-stone-100">
          <div className="px-4 py-2 bg-stone-50 rounded-xl border border-stone-200 text-center">
            <span className="block text-lg font-black text-stone-900">{orders.length}</span>
            <span className="text-[11px] text-stone-500">Orders</span>
          </div>

          <div className="px-4 py-2 bg-stone-50 rounded-xl border border-stone-200 text-center">
            <span className="block text-lg font-black text-stone-900">{allPurchasedItems.length}</span>
            <span className="text-[11px] text-stone-500">Assets</span>
          </div>

          <button
            id="account-logout-btn"
            type="button"
            onClick={logout}
            className="inline-flex items-center gap-1.5 px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 hover:text-stone-900 text-xs font-semibold rounded-xl transition cursor-pointer"
          >
            <LogOut className="w-3.5 h-3.5" />
            <span>Sign Out</span>
          </button>
        </div>
      </div>

      {/* Tabs Navigation */}
      <div className="flex border-b border-stone-200 gap-6 text-sm font-semibold">
        <button
          id="tab-order-history"
          type="button"
          onClick={() => setActiveTab('orders')}
          className={`pb-3 transition relative cursor-pointer ${
            activeTab === 'orders'
              ? 'text-stone-900 border-b-2 border-stone-900'
              : 'text-stone-500 hover:text-stone-700'
          }`}
        >
          Order History ({orders.length})
        </button>

        <button
          id="tab-downloads"
          type="button"
          onClick={() => setActiveTab('downloads')}
          className={`pb-3 transition relative cursor-pointer ${
            activeTab === 'downloads'
              ? 'text-stone-900 border-b-2 border-stone-900'
              : 'text-stone-500 hover:text-stone-700'
          }`}
        >
          My Downloads ({allPurchasedItems.length})
        </button>

        <button
          id="tab-settings"
          type="button"
          onClick={() => setActiveTab('settings')}
          className={`pb-3 transition relative cursor-pointer ${
            activeTab === 'settings'
              ? 'text-stone-900 border-b-2 border-stone-900'
              : 'text-stone-500 hover:text-stone-700'
          }`}
        >
          Account Preferences
        </button>
      </div>

      {/* TAB CONTENT: Orders */}
      {activeTab === 'orders' && (
        <div id="account-orders-section" className="space-y-6">
          {orders.length > 0 ? (
            orders.map((order) => {
              const formattedDate = new Date(order.createdAt).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
              });

              return (
                <div
                  key={order.id}
                  id={`order-card-${order.id}`}
                  className="bg-white rounded-2xl border border-stone-200 p-5 sm:p-6 shadow-2xs space-y-4"
                >
                  {/* Order Card Header */}
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-stone-100">
                    <div className="flex items-center gap-3">
                      <div className="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center text-stone-700">
                        <PackageCheck className="w-5 h-5" />
                      </div>
                      <div>
                        <div className="flex items-center gap-2">
                          <h3 className="text-sm sm:text-base font-bold text-stone-900">
                            Order {order.id}
                          </h3>
                          <span className="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-md">
                            {order.status}
                          </span>
                        </div>
                        <p className="text-xs text-stone-400 mt-0.5">Placed on {formattedDate}</p>
                      </div>
                    </div>

                    <div className="text-left sm:text-right mt-2 sm:mt-0">
                      <span className="block text-sm sm:text-base font-extrabold text-stone-900">
                        ${order.total.toFixed(2)}
                      </span>
                      <span className="text-xs text-stone-400">
                        {order.items.reduce((s, i) => s + i.quantity, 0)} items • {order.paymentMethod}
                      </span>
                    </div>
                  </div>

                  {/* Order Items */}
                  <div className="space-y-3">
                    {order.items.map((item, idx) => (
                      <div
                        key={idx}
                        className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-stone-50/70 rounded-xl border border-stone-100"
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
                              Qty: {item.quantity} × ${item.price} • Commercial License
                            </p>
                          </div>
                        </div>

                        <div className="flex items-center gap-2 self-end sm:self-center">
                          <Link
                            href={`/products/${item.productId}`}
                            className="px-3 py-1.5 bg-white hover:bg-stone-100 text-stone-700 text-xs font-semibold rounded-lg border border-stone-200 transition"
                          >
                            View Product
                          </Link>

                          <button
                            type="button"
                            onClick={() => handleDownload(item.title)}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-lg transition cursor-pointer"
                          >
                            <DownloadCloud className="w-3.5 h-3.5 text-amber-300" />
                            <span>Download Files</span>
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>

                </div>
              );
            })
          ) : (
            <div className="p-12 text-center bg-white rounded-2xl border border-stone-200">
              <ShoppingBag className="w-10 h-10 text-stone-300 mx-auto mb-3" />
              <h3 className="text-base font-bold text-stone-900">No orders yet</h3>
              <p className="text-xs text-stone-500 mt-1">Browse the marketplace to start adding tools and assets.</p>
              <Link
                href="/products"
                className="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-stone-900 text-white text-xs font-semibold rounded-xl hover:bg-stone-800 transition"
              >
                Explore Catalog
              </Link>
            </div>
          )}
        </div>
      )}

      {/* TAB CONTENT: Consolidated Downloads */}
      {activeTab === 'downloads' && (
        <div id="account-downloads-section" className="space-y-4">
          <div className="bg-white rounded-2xl border border-stone-200 p-6 shadow-2xs">
            <h3 className="text-base font-bold text-stone-900 mb-1">Available Digital Downloads</h3>
            <p className="text-xs text-stone-500 mb-6">
              All source assets, templates, and zip archives you have purchased are available with perpetual lifetime updates.
            </p>

            <div className="space-y-3">
              {allPurchasedItems.map((item, idx) => (
                <div
                  key={idx}
                  className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-stone-200 hover:border-stone-300 bg-stone-50/50 transition"
                >
                  <div className="flex items-center gap-3.5 min-w-0">
                    <div className="relative w-14 h-14 rounded-xl overflow-hidden bg-stone-200 shrink-0">
                      <Image
                        src={item.image}
                        alt={item.title}
                        fill
                        sizes="56px"
                        className="object-cover"
                        referrerPolicy="no-referrer"
                      />
                    </div>
                    <div className="min-w-0">
                      <h4 className="text-sm font-bold text-stone-900 truncate">
                        {item.title}
                      </h4>
                      <div className="flex items-center gap-3 text-xs text-stone-500 mt-1">
                        <span className="flex items-center gap-1">
                          <FileCode className="w-3.5 h-3.5 text-stone-400" /> Source Files
                        </span>
                        <span>•</span>
                        <span className="flex items-center gap-1 text-emerald-600">
                          <ShieldCheck className="w-3.5 h-3.5" /> Commercial License
                        </span>
                      </div>
                    </div>
                  </div>

                  <button
                    type="button"
                    onClick={() => handleDownload(item.title)}
                    className="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-stone-900 hover:bg-stone-800 active:bg-stone-950 text-white text-xs font-semibold rounded-xl transition shrink-0 cursor-pointer shadow-2xs"
                  >
                    <DownloadCloud className="w-3.5 h-3.5 text-amber-300" />
                    <span>Download Package (ZIP)</span>
                  </button>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* TAB CONTENT: Settings & Profile Details */}
      {activeTab === 'settings' && (
        <div id="account-settings-section" className="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-2xs max-w-2xl space-y-6">
          <div>
            <h3 className="text-base font-bold text-stone-900">Account Preferences</h3>
            <p className="text-xs text-stone-500 mt-1">Manage your customer profile and notifications.</p>
          </div>

          <div className="space-y-4 text-xs">
            <div>
              <label className="block font-semibold text-stone-700 mb-1">Display Name</label>
              <input
                type="text"
                defaultValue={currentUser.name}
                className="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:outline-none focus:bg-white"
              />
            </div>

            <div>
              <label className="block font-semibold text-stone-700 mb-1">Email Address</label>
              <input
                type="email"
                defaultValue={currentUser.email}
                className="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:outline-none focus:bg-white"
              />
            </div>

            <div className="pt-2">
              <button
                type="button"
                onClick={() => showToast('Profile preferences updated')}
                className="px-5 py-2.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold rounded-xl transition cursor-pointer"
              >
                Save Changes
              </button>
            </div>
          </div>
        </div>
      )}

    </div>
  );
}
