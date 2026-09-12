'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { ShoppingBag, Search, User, Menu, X, Sparkles } from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';

export function Navbar() {
  const { cartCount, currentUser } = useMarketplace();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const router = useRouter();

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      router.push(`/products?q=${encodeURIComponent(searchQuery.trim())}`);
      setMobileMenuOpen(false);
    } else {
      router.push('/products');
    }
  };

  return (
    <header id="marketplace-header" className="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-stone-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 gap-4">
          
          {/* Brand Logo */}
          <Link
            id="brand-logo"
            href="/"
            className="flex items-center gap-2.5 font-bold text-lg sm:text-xl text-stone-900 shrink-0 group hover:opacity-90 transition-opacity"
          >
            <div className="w-9 h-9 rounded-xl bg-stone-900 text-white flex items-center justify-center shadow-sm">
              <Sparkles className="w-5 h-5 text-amber-300" />
            </div>
            <span className="tracking-tight">Digital Marketplace</span>
          </Link>

          {/* Search Bar - Desktop */}
          <form
            id="header-search-form"
            onSubmit={handleSearchSubmit}
            className="hidden md:flex items-center flex-1 max-w-md mx-4"
          >
            <div className="relative w-full">
              <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" />
              <input
                id="nav-search-input"
                type="text"
                placeholder="Search templates, UI kits, fonts, boilerplates..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-9 pr-4 py-2 bg-stone-100/80 hover:bg-stone-100 focus:bg-white text-sm text-stone-800 placeholder-stone-400 border border-stone-200 rounded-full focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-400 transition"
              />
            </div>
          </form>

          {/* Desktop Navigation Links */}
          <nav id="desktop-nav-links" className="hidden md:flex items-center gap-6 text-sm font-medium text-stone-600">
            <Link
              id="nav-link-products"
              href="/products"
              className="hover:text-stone-900 transition-colors py-1"
            >
              Browse Products
            </Link>

            {currentUser ? (
              <Link
                id="nav-link-account"
                href="/account"
                className="flex items-center gap-1.5 hover:text-stone-900 transition-colors py-1"
              >
                <User className="w-4 h-4 text-stone-500" />
                <span className="max-w-[120px] truncate">{currentUser.name.split(' ')[0]}</span>
              </Link>
            ) : (
              <Link
                id="nav-link-login"
                href="/login"
                className="hover:text-stone-900 transition-colors py-1"
              >
                Sign In
              </Link>
            )}

            {/* Cart Button */}
            <Link
              id="nav-cart-btn"
              href="/cart"
              className="relative flex items-center gap-2 px-3.5 py-2 bg-stone-900 text-white rounded-full hover:bg-stone-800 transition shadow-xs text-xs font-semibold"
            >
              <ShoppingBag className="w-4 h-4 text-amber-300" />
              <span>Cart</span>
              {cartCount > 0 && (
                <span
                  id="nav-cart-badge"
                  className="w-5 h-5 bg-amber-400 text-stone-950 font-bold rounded-full flex items-center justify-center text-xs"
                >
                  {cartCount}
                </span>
              )}
            </Link>
          </nav>

          {/* Mobile Right Controls */}
          <div className="flex items-center gap-2 md:hidden">
            <Link
              id="mobile-cart-btn"
              href="/cart"
              className="relative p-2 text-stone-700 hover:text-stone-900"
              aria-label="View shopping cart"
            >
              <ShoppingBag className="w-6 h-6" />
              {cartCount > 0 && (
                <span className="absolute top-1 right-1 w-4 h-4 bg-stone-900 text-white font-bold rounded-full flex items-center justify-center text-[10px]">
                  {cartCount}
                </span>
              )}
            </Link>

            <button
              id="mobile-menu-toggle"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="p-2 text-stone-700 hover:text-stone-900 focus:outline-none"
              aria-label="Toggle navigation menu"
            >
              {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>

        {/* Mobile Flyout Menu */}
        {mobileMenuOpen && (
          <div id="mobile-navigation-panel" className="md:hidden py-4 border-t border-stone-200 space-y-3">
            <form onSubmit={handleSearchSubmit} className="relative">
              <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" />
              <input
                id="mobile-search-input"
                type="text"
                placeholder="Search products..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-9 pr-4 py-2 bg-stone-100 text-sm text-stone-900 border border-stone-200 rounded-lg focus:outline-none focus:border-stone-400"
              />
            </form>

            <div className="flex flex-col space-y-2 pt-2 text-sm font-medium">
              <Link
                href="/products"
                onClick={() => setMobileMenuOpen(false)}
                className="px-3 py-2 rounded-lg hover:bg-stone-100 text-stone-700"
              >
                Browse All Products
              </Link>
              <Link
                href="/cart"
                onClick={() => setMobileMenuOpen(false)}
                className="px-3 py-2 rounded-lg hover:bg-stone-100 text-stone-700 flex items-center justify-between"
              >
                <span>Shopping Cart</span>
                <span className="font-semibold text-stone-900">{cartCount} items</span>
              </Link>
              {currentUser ? (
                <Link
                  href="/account"
                  onClick={() => setMobileMenuOpen(false)}
                  className="px-3 py-2 rounded-lg hover:bg-stone-100 text-stone-700"
                >
                  My Account ({currentUser.name})
                </Link>
              ) : (
                <Link
                  href="/login"
                  onClick={() => setMobileMenuOpen(false)}
                  className="px-3 py-2 rounded-lg hover:bg-stone-100 text-stone-700"
                >
                  Sign In / Register
                </Link>
              )}
            </div>
          </div>
        )}

      </div>
    </header>
  );
}
