'use client';

import React from 'react';
import Link from 'next/link';
import { Sparkles, ShieldCheck, Zap, DownloadCloud, Heart } from 'lucide-react';

export function Footer() {
  return (
    <footer id="marketplace-footer" className="bg-stone-900 text-stone-300 border-t border-stone-800 mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        
        {/* Value Props Row */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 pb-12 border-b border-stone-800">
          <div className="flex items-start gap-4">
            <div className="w-10 h-10 rounded-xl bg-stone-800 flex items-center justify-center text-amber-400 shrink-0">
              <DownloadCloud className="w-5 h-5" />
            </div>
            <div>
              <h4 className="text-white font-semibold text-sm">Instant Digital Delivery</h4>
              <p className="text-xs text-stone-400 mt-1 leading-relaxed">
                Receive access links, source repositories, and design asset files the moment payment completes.
              </p>
            </div>
          </div>

          <div className="flex items-start gap-4">
            <div className="w-10 h-10 rounded-xl bg-stone-800 flex items-center justify-center text-emerald-400 shrink-0">
              <ShieldCheck className="w-5 h-5" />
            </div>
            <div>
              <h4 className="text-white font-semibold text-sm">Verified Creator Quality</h4>
              <p className="text-xs text-stone-400 mt-1 leading-relaxed">
                Every boilerplate, UI kit, and asset pack is tested for clean code, typography scale, and standards.
              </p>
            </div>
          </div>

          <div className="flex items-start gap-4">
            <div className="w-10 h-10 rounded-xl bg-stone-800 flex items-center justify-center text-indigo-400 shrink-0">
              <Zap className="w-5 h-5" />
            </div>
            <div>
              <h4 className="text-white font-semibold text-sm">Lifetime Updates</h4>
              <p className="text-xs text-stone-400 mt-1 leading-relaxed">
                Download future framework updates, bugfixes, and expansions directly from your personal dashboard.
              </p>
            </div>
          </div>
        </div>

        {/* Navigation & Columns */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 py-12">
          
          {/* Brand Info */}
          <div className="space-y-4">
            <div className="flex items-center gap-2.5">
              <div className="w-8 h-8 rounded-lg bg-white text-stone-950 flex items-center justify-center font-bold">
                <Sparkles className="w-4 h-4 text-amber-500" />
              </div>
              <span className="text-white font-bold text-base tracking-tight">Digital Marketplace</span>
            </div>
            <p className="text-xs text-stone-400 leading-relaxed">
              Curated digital goods for designers, developers, creators, and modern product builders.
            </p>
            <p className="text-xs text-stone-500">
              Built with Next.js App Router and Tailwind CSS.
            </p>
          </div>

          {/* Catalog Categories */}
          <div>
            <h5 className="text-white font-semibold text-xs uppercase tracking-wider mb-4">Explore Marketplace</h5>
            <ul className="space-y-2.5 text-xs text-stone-400">
              <li>
                <Link href="/products?category=UI+%26+Design+Kits" className="hover:text-white transition">
                  UI & Design Kits
                </Link>
              </li>
              <li>
                <Link href="/products?category=Developer+Boilerplates" className="hover:text-white transition">
                  Developer Boilerplates
                </Link>
              </li>
              <li>
                <Link href="/products?category=Fonts+%26+Typography" className="hover:text-white transition">
                  Fonts & Typography
                </Link>
              </li>
              <li>
                <Link href="/products?category=3D+Assets+%26+Icons" className="hover:text-white transition">
                  3D Assets & Icons
                </Link>
              </li>
              <li>
                <Link href="/products?category=Productivity+Templates" className="hover:text-white transition">
                  Productivity Templates
                </Link>
              </li>
            </ul>
          </div>

          {/* Quick Links */}
          <div>
            <h5 className="text-white font-semibold text-xs uppercase tracking-wider mb-4">User Center</h5>
            <ul className="space-y-2.5 text-xs text-stone-400">
              <li>
                <Link href="/account" className="hover:text-white transition">
                  Order History & Downloads
                </Link>
              </li>
              <li>
                <Link href="/cart" className="hover:text-white transition">
                  Active Shopping Cart
                </Link>
              </li>
              <li>
                <Link href="/checkout" className="hover:text-white transition">
                  Checkout Portal
                </Link>
              </li>
              <li>
                <Link href="/login" className="hover:text-white transition">
                  Sign In / Create Account
                </Link>
              </li>
            </ul>
          </div>

          {/* Legal / Licensing */}
          <div>
            <h5 className="text-white font-semibold text-xs uppercase tracking-wider mb-4">Standard License</h5>
            <p className="text-xs text-stone-400 leading-relaxed mb-3">
              All digital marketplace items include royalty-free commercial project use with single or team attribution waivers.
            </p>
            <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-stone-800 text-stone-300 text-xs border border-stone-700">
              <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
              <span>Commercial Ready</span>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="pt-8 border-t border-stone-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-500">
          <p>© {new Date().getFullYear()} Digital Marketplace. All rights reserved.</p>
          <div className="flex items-center gap-6">
            <Link href="/products" className="hover:text-stone-300 transition">
              Products
            </Link>
            <Link href="/cart" className="hover:text-stone-300 transition">
              Cart
            </Link>
            <Link href="/account" className="hover:text-stone-300 transition">
              Dashboard
            </Link>
          </div>
        </div>

      </div>
    </footer>
  );
}
