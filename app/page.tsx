'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import {
  ArrowRight,
  Sparkles,
  Layers,
  Code2,
  Type,
  Music,
  Box,
  CheckCircle2,
  DownloadCloud,
  ShieldCheck,
  TrendingUp,
  Search,
} from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';
import { ProductCard } from '@/components/ProductCard';

const CATEGORY_ITEMS = [
  { name: 'UI & Design Kits', icon: Layers, count: '120+ assets', query: 'UI & Design Kits' },
  { name: 'Developer Boilerplates', icon: Code2, count: '85+ stacks', query: 'Developer Boilerplates' },
  { name: 'Fonts & Typography', icon: Type, count: '45+ families', query: 'Fonts & Typography' },
  { name: 'Audio & SFX Packs', icon: Music, count: '60+ packs', query: 'Audio & SFX Packs' },
  { name: '3D Assets & Icons', icon: Box, count: '90+ sets', query: '3D Assets & Icons' },
  { name: 'Productivity Templates', icon: TrendingUp, count: '110+ systems', query: 'Productivity Templates' },
];

export default function HomePage() {
  const { products } = useMarketplace();
  const [heroSearch, setHeroSearch] = useState('');
  const router = useRouter();

  const featuredProducts = products.filter((p) => p.isFeatured).slice(0, 4);
  const trendingProducts = products.slice(0, 6);

  const handleHeroSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (heroSearch.trim()) {
      router.push(`/products?q=${encodeURIComponent(heroSearch.trim())}`);
    } else {
      router.push('/products');
    }
  };

  return (
    <div id="home-page-container" className="flex flex-col gap-16 md:gap-24 pb-16">
      
      {/* Hero Section */}
      <section id="hero-section" className="relative pt-12 pb-16 md:pt-20 md:pb-24 overflow-hidden border-b border-stone-200 bg-linear-to-b from-stone-100/80 via-stone-50 to-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col items-center text-center max-w-3xl mx-auto">
            
            {/* Top Badge */}
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-stone-200/80 text-stone-800 text-xs font-semibold mb-6 border border-stone-300/50">
              <Sparkles className="w-3.5 h-3.5 text-amber-500" />
              <span>Curated Digital Marketplace for Creators & Engineers</span>
            </div>

            {/* Main Headline */}
            <h1 className="text-3xl sm:text-5xl lg:text-6xl font-black text-stone-900 tracking-tight leading-[1.12]">
              Craft better products with premium digital assets.
            </h1>

            {/* Subheading */}
            <p className="mt-5 text-base sm:text-lg text-stone-600 leading-relaxed max-w-2xl">
              Browse world-class UI design systems, full-stack boilerplates, high-fidelity 3D packs, and productivity templates with instant delivery and commercial licenses.
            </p>

            {/* Hero Search Box */}
            <form
              onSubmit={handleHeroSearch}
              className="mt-8 w-full max-w-xl flex items-center bg-white rounded-full p-1.5 border border-stone-300 shadow-sm focus-within:ring-2 focus-within:ring-stone-900 focus-within:border-stone-900 transition"
            >
              <div className="pl-3.5 text-stone-400">
                <Search className="w-5 h-5" />
              </div>
              <input
                type="text"
                value={heroSearch}
                onChange={(e) => setHeroSearch(e.target.value)}
                placeholder="Search templates, fonts, design kits, boilerplates..."
                className="w-full px-3 py-2 text-sm text-stone-900 placeholder-stone-400 bg-transparent focus:outline-none"
              />
              <button
                type="submit"
                className="px-5 py-2.5 bg-stone-900 hover:bg-stone-800 active:bg-stone-950 text-white text-xs sm:text-sm font-semibold rounded-full transition shrink-0 cursor-pointer shadow-xs"
              >
                Find Assets
              </button>
            </form>

            {/* Quick Action Chips & Metrics */}
            <div className="mt-8 flex flex-wrap items-center justify-center gap-6 text-xs text-stone-500">
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                <span>Verified Code & Files</span>
              </div>
              <div className="flex items-center gap-2">
                <DownloadCloud className="w-4 h-4 text-emerald-600" />
                <span>Instant Digital Downloads</span>
              </div>
              <div className="flex items-center gap-2">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                <span>Commercial Project Rights</span>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* Categories Grid */}
      <section id="categories-section" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div className="flex items-center justify-between mb-8">
          <div>
            <h2 className="text-2xl sm:text-3xl font-bold text-stone-900 tracking-tight">
              Explore Categories
            </h2>
            <p className="text-sm text-stone-500 mt-1">
              Find exactly what you need for your next build
            </p>
          </div>
          <Link
            href="/products"
            className="text-xs sm:text-sm font-semibold text-stone-900 hover:text-stone-600 flex items-center gap-1 group"
          >
            <span>View All</span>
            <ArrowRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
          </Link>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
          {CATEGORY_ITEMS.map((cat) => {
            const Icon = cat.icon;
            return (
              <Link
                key={cat.name}
                href={`/products?category=${encodeURIComponent(cat.query)}`}
                className="category-card flex flex-col items-center text-center p-5 bg-white rounded-2xl border border-stone-200 hover:border-stone-400 hover:shadow-xs transition group"
              >
                <div className="w-12 h-12 rounded-xl bg-stone-100 flex items-center justify-center text-stone-800 group-hover:bg-stone-900 group-hover:text-white transition-colors mb-3">
                  <Icon className="w-6 h-6" />
                </div>
                <h3 className="text-xs font-bold text-stone-900 leading-snug group-hover:text-stone-700">
                  {cat.name}
                </h3>
                <span className="text-[11px] text-stone-400 mt-1">
                  {cat.count}
                </span>
              </Link>
            );
          })}
        </div>
      </section>

      {/* Featured Products Showcase */}
      <section id="featured-products-section" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div className="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
          <div>
            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-100 text-amber-900 text-xs font-semibold mb-2">
              <Sparkles className="w-3.5 h-3.5 text-amber-600" />
              <span>Staff Picks</span>
            </div>
            <h2 className="text-2xl sm:text-3xl font-bold text-stone-900 tracking-tight">
              Featured Products
            </h2>
            <p className="text-sm text-stone-500 mt-1">
              Top-rated digital tools loved by creators worldwide
            </p>
          </div>

          <Link
            href="/products"
            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-stone-200 text-xs sm:text-sm font-semibold text-stone-800 hover:bg-stone-50 shadow-2xs transition"
          >
            <span>Browse Full Catalog</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {featuredProducts.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      </section>

      {/* Marketplace Benefit Banner */}
      <section id="trust-banner-section" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div className="bg-stone-900 rounded-3xl p-8 sm:p-12 text-white relative overflow-hidden">
          <div className="max-w-2xl relative z-10">
            <span className="px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-semibold border border-amber-400/30">
              Creator Marketplace Guarantee
            </span>
            <h2 className="text-2xl sm:text-4xl font-extrabold tracking-tight mt-4 text-white">
              Built by developers & designers, for developers & designers.
            </h2>
            <p className="text-sm sm:text-base text-stone-300 mt-4 leading-relaxed">
              Never start from a blank canvas again. Every asset is strictly audited for clean structure, standard naming conventions, and instant production integration.
            </p>
            <div className="mt-8 flex flex-wrap gap-4">
              <Link
                href="/products"
                className="px-6 py-3 bg-amber-400 text-stone-950 hover:bg-amber-300 text-sm font-bold rounded-xl transition shadow-sm"
              >
                Explore All Products
              </Link>
              <Link
                href="/login"
                className="px-6 py-3 bg-stone-800 hover:bg-stone-700 text-white text-sm font-semibold rounded-xl transition border border-stone-700"
              >
                Join as Customer
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Trending & Recent Products */}
      <section id="trending-products-section" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div className="flex items-center justify-between mb-8">
          <div>
            <h2 className="text-2xl sm:text-3xl font-bold text-stone-900 tracking-tight">
              Trending Marketplace Items
            </h2>
            <p className="text-sm text-stone-500 mt-1">
              Freshly released assets and popular toolkits
            </p>
          </div>
          <Link
            href="/products"
            className="text-xs sm:text-sm font-semibold text-stone-900 hover:text-stone-600 flex items-center gap-1 group"
          >
            <span>See All ({products.length})</span>
            <ArrowRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {trendingProducts.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      </section>

    </div>
  );
}
