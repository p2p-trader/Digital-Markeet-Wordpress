'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Star, ShoppingCart, ArrowRight } from 'lucide-react';
import { Product } from '@/types/marketplace';
import { useMarketplace } from '@/context/MarketplaceContext';

interface ProductCardProps {
  product: Product;
}

export function ProductCard({ product }: ProductCardProps) {
  const { addToCart } = useMarketplace();

  const handleAddToCartClick = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    addToCart(product, 1);
  };

  return (
    <article
      id={`product-card-${product.id}`}
      className="product-card group flex flex-col bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5"
    >
      {/* Product Image Box */}
      <Link
        href={`/products/${product.id}`}
        className="relative block aspect-[16/10] w-full overflow-hidden bg-stone-100"
      >
        <Image
          src={product.image}
          alt={product.title}
          fill
          sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
          className="object-cover group-hover:scale-105 transition-transform duration-300"
          referrerPolicy="no-referrer"
        />

        {/* Category Badge */}
        <div className="absolute top-3 left-3">
          <span className="px-2.5 py-1 text-xs font-semibold rounded-full bg-stone-900/85 text-white backdrop-blur-xs shadow-xs">
            {product.category}
          </span>
        </div>

        {product.isFeatured && (
          <div className="absolute top-3 right-3">
            <span className="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-400 text-stone-950 shadow-xs">
              Featured
            </span>
          </div>
        )}
      </Link>

      {/* Card Details */}
      <div className="flex flex-col flex-1 p-5">
        
        {/* Rating & Author */}
        <div className="flex items-center justify-between gap-2 text-xs text-stone-500 mb-2">
          <span className="truncate">By {product.author.name}</span>
          <div className="flex items-center gap-1 text-amber-500 shrink-0 font-medium">
            <Star className="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
            <span className="text-stone-700 font-semibold">{product.rating}</span>
            <span className="text-stone-400">({product.reviewCount})</span>
          </div>
        </div>

        {/* Product Title */}
        <h3 className="text-base font-bold text-stone-900 line-clamp-2 leading-snug group-hover:text-stone-700 transition-colors mb-2">
          <Link href={`/products/${product.id}`}>
            {product.title}
          </Link>
        </h3>

        {/* Short Description */}
        <p className="text-xs text-stone-500 line-clamp-2 leading-relaxed mb-4 flex-1">
          {product.description}
        </p>

        {/* Meta tags */}
        <div className="flex flex-wrap gap-1.5 mb-4">
          {product.tags.slice(0, 3).map((tag) => (
            <span
              key={tag}
              className="px-2 py-0.5 text-[11px] font-medium bg-stone-100 text-stone-600 rounded-md"
            >
              {tag}
            </span>
          ))}
        </div>

        {/* Price & Action Row */}
        <div className="pt-3 border-t border-stone-100 flex items-center justify-between gap-3 mt-auto">
          <div className="flex items-baseline gap-2">
            <span className="text-xl font-black text-stone-900">
              ${product.price}
            </span>
            {product.originalPrice && (
              <span className="text-xs text-stone-400 line-through">
                ${product.originalPrice}
              </span>
            )}
          </div>

          <div className="flex items-center gap-2">
            <button
              id={`quick-add-btn-${product.id}`}
              onClick={handleAddToCartClick}
              type="button"
              title="Add to cart"
              className="p-2 text-stone-700 bg-stone-100 hover:bg-stone-200 active:bg-stone-300 rounded-xl transition cursor-pointer"
              aria-label={`Add ${product.title} to cart`}
            >
              <ShoppingCart className="w-4 h-4" />
            </button>

            <Link
              id={`view-details-btn-${product.id}`}
              href={`/products/${product.id}`}
              className="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold text-white bg-stone-900 hover:bg-stone-800 active:bg-stone-950 rounded-xl transition shadow-2xs"
            >
              <span>Details</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>
        </div>

      </div>
    </article>
  );
}
