'use client';

import React, { useState, useMemo, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import { Search, SlidersHorizontal, ArrowUpDown, X, Tag } from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';
import { ProductCard } from '@/components/ProductCard';
import { MOCK_CATEGORIES } from '@/data/mock-products';

function ProductListContent() {
  const { products } = useMarketplace();
  const searchParams = useSearchParams();

  const initialCategory = searchParams.get('category') || 'All Products';
  const initialQuery = searchParams.get('q') || '';

  const [selectedCategory, setSelectedCategory] = useState<string>(initialCategory);
  const [searchTerm, setSearchTerm] = useState<string>(initialQuery);
  const [sortBy, setSortBy] = useState<'featured' | 'price-asc' | 'price-desc' | 'rating'>('featured');
  const [selectedTag, setSelectedTag] = useState<string | null>(null);

  // Extract all unique tags
  const allTags = useMemo(() => {
    const set = new Set<string>();
    products.forEach((p) => p.tags.forEach((t) => set.add(t)));
    return Array.from(set);
  }, [products]);

  // Filter & sort logic
  const filteredProducts = useMemo(() => {
    return products
      .filter((p) => {
        // Category filter
        if (selectedCategory !== 'All Products' && p.category !== selectedCategory) {
          return false;
        }
        // Search filter
        if (searchTerm.trim()) {
          const q = searchTerm.toLowerCase();
          const matchTitle = p.title.toLowerCase().includes(q);
          const matchDesc = p.description.toLowerCase().includes(q);
          const matchTag = p.tags.some((t) => t.toLowerCase().includes(q));
          const matchCategory = p.category.toLowerCase().includes(q);
          if (!matchTitle && !matchDesc && !matchTag && !matchCategory) {
            return false;
          }
        }
        // Tag filter
        if (selectedTag && !p.tags.includes(selectedTag)) {
          return false;
        }
        return true;
      })
      .sort((a, b) => {
        if (sortBy === 'price-asc') return a.price - b.price;
        if (sortBy === 'price-desc') return b.price - a.price;
        if (sortBy === 'rating') return b.rating - a.rating;
        return (b.isFeatured ? 1 : 0) - (a.isFeatured ? 1 : 0);
      });
  }, [products, selectedCategory, searchTerm, selectedTag, sortBy]);

  const handleResetFilters = () => {
    setSelectedCategory('All Products');
    setSearchTerm('');
    setSelectedTag(null);
    setSortBy('featured');
  };

  return (
    <div id="product-listing-page" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex flex-col gap-8">
      
      {/* Page Header */}
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-stone-200 pb-6">
        <div>
          <h1 className="text-3xl font-extrabold text-stone-900 tracking-tight">
            Marketplace Catalog
          </h1>
          <p className="text-sm text-stone-500 mt-1">
            Browse high quality UI kits, boilerplates, audio packs, and templates with verified source files.
          </p>
        </div>

        {/* Total count badge */}
        <div className="text-xs font-semibold text-stone-500 bg-white px-3 py-1.5 rounded-lg border border-stone-200 self-start md:self-auto">
          Showing <span className="text-stone-900 font-bold">{filteredProducts.length}</span> of {products.length} products
        </div>
      </div>

      {/* Filter and Search Controls */}
      <div className="flex flex-col gap-4">
        
        {/* Search Bar & Sort Dropdown */}
        <div className="flex flex-col sm:flex-row gap-3">
          <div className="relative flex-1">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" />
            <input
              id="catalog-search-input"
              type="text"
              placeholder="Search by keywords, framework, design system..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-10 py-2.5 bg-white border border-stone-200 rounded-xl text-sm text-stone-900 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-400 transition"
            />
            {searchTerm && (
              <button
                type="button"
                onClick={() => setSearchTerm('')}
                className="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-stone-400 hover:text-stone-600 rounded"
                aria-label="Clear search input"
              >
                <X className="w-4 h-4" />
              </button>
            )}
          </div>

          <div className="flex items-center gap-2">
            <div className="relative flex-1 sm:w-52">
              <ArrowUpDown className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400 pointer-events-none" />
              <select
                id="catalog-sort-select"
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value as any)}
                className="w-full pl-9 pr-8 py-2.5 bg-white border border-stone-200 rounded-xl text-sm text-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-400 appearance-none transition cursor-pointer"
              >
                <option value="featured">Featured / Recommended</option>
                <option value="rating">Highest Rated</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
              </select>
            </div>
          </div>
        </div>

        {/* Category Pills */}
        <div id="category-pills" className="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
          {MOCK_CATEGORIES.map((cat) => {
            const isActive = selectedCategory === cat;
            return (
              <button
                key={cat}
                id={`cat-pill-${cat.toLowerCase().replace(/[^a-z0-9]/g, '-')}`}
                type="button"
                onClick={() => setSelectedCategory(cat)}
                className={`px-3.5 py-1.5 text-xs font-semibold rounded-full whitespace-nowrap transition cursor-pointer ${
                  isActive
                    ? 'bg-stone-900 text-white shadow-xs'
                    : 'bg-white text-stone-600 border border-stone-200 hover:bg-stone-100'
                }`}
              >
                {cat}
              </button>
            );
          })}
        </div>

        {/* Active Tag Filters */}
        <div className="flex items-center gap-2 flex-wrap text-xs pt-1">
          <span className="text-stone-400 flex items-center gap-1">
            <Tag className="w-3 h-3" /> Quick tags:
          </span>
          {allTags.slice(0, 8).map((tag) => {
            const isSelected = selectedTag === tag;
            return (
              <button
                key={tag}
                type="button"
                onClick={() => setSelectedTag(isSelected ? null : tag)}
                className={`px-2.5 py-1 rounded-md transition cursor-pointer ${
                  isSelected
                    ? 'bg-stone-800 text-white font-semibold'
                    : 'bg-stone-200/70 text-stone-700 hover:bg-stone-200'
                }`}
              >
                #{tag}
              </button>
            );
          })}
          {(selectedCategory !== 'All Products' || searchTerm || selectedTag) && (
            <button
              type="button"
              onClick={handleResetFilters}
              className="text-xs text-rose-600 hover:underline font-semibold ml-2 cursor-pointer"
            >
              Reset Filters
            </button>
          )}
        </div>

      </div>

      {/* Products Grid */}
      {filteredProducts.length > 0 ? (
        <div
          id="products-grid"
          className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
        >
          {filteredProducts.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      ) : (
        /* Empty State */
        <div id="catalog-empty-state" className="flex flex-col items-center justify-center p-12 bg-white rounded-2xl border border-stone-200 text-center my-8">
          <div className="w-12 h-12 rounded-full bg-stone-100 flex items-center justify-center text-stone-400 mb-4">
            <Search className="w-6 h-6" />
          </div>
          <h3 className="text-lg font-bold text-stone-900">No products match your criteria</h3>
          <p className="text-sm text-stone-500 mt-1 max-w-md">
            Try adjusting your search query, switching categories, or clearing tags.
          </p>
          <button
            type="button"
            onClick={handleResetFilters}
            className="mt-5 px-4 py-2 bg-stone-900 text-white text-xs font-semibold rounded-xl hover:bg-stone-800 transition"
          >
            Clear all filters
          </button>
        </div>
      )}

    </div>
  );
}

export default function ProductListingPage() {
  return (
    <Suspense fallback={
      <div className="max-w-7xl mx-auto px-4 py-12 text-center text-stone-500 text-sm">
        Loading catalog...
      </div>
    }>
      <ProductListContent />
    </Suspense>
  );
}
