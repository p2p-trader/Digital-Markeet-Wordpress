'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Minus, Plus, Trash2 } from 'lucide-react';
import { CartItem as CartItemType } from '@/types/marketplace';
import { useMarketplace } from '@/context/MarketplaceContext';

interface CartItemProps {
  item: CartItemType;
}

export function CartItem({ item }: CartItemProps) {
  const { updateQuantity, removeFromCart } = useMarketplace();
  const { product, quantity } = item;

  return (
    <div
      id={`cart-item-${product.id}`}
      className="cart-item flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 sm:p-5 bg-white rounded-xl border border-stone-200 shadow-2xs hover:border-stone-300 transition"
    >
      {/* Product Image & Info */}
      <div className="flex items-center gap-4 flex-1 min-w-0">
        <Link
          href={`/products/${product.id}`}
          className="relative w-20 h-20 sm:w-24 sm:h-20 rounded-lg overflow-hidden bg-stone-100 shrink-0 border border-stone-100"
        >
          <Image
            src={product.image}
            alt={product.title}
            fill
            sizes="96px"
            className="object-cover"
            referrerPolicy="no-referrer"
          />
        </Link>

        <div className="flex-1 min-w-0">
          <span className="inline-block px-2 py-0.5 text-[10px] font-semibold text-stone-600 bg-stone-100 rounded-md mb-1">
            {product.category}
          </span>
          <h4 className="text-sm sm:text-base font-bold text-stone-900 truncate">
            <Link
              href={`/products/${product.id}`}
              className="hover:text-stone-700 transition"
            >
              {product.title}
            </Link>
          </h4>
          <p className="text-xs text-stone-500 mt-0.5">
            Format: {product.fileFormat} • License: Commercial
          </p>
          <p className="text-xs font-semibold text-stone-900 sm:hidden mt-1">
            ${product.price} each
          </p>
        </div>
      </div>

      {/* Quantity Controls & Subtotal */}
      <div className="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-stone-100">
        
        {/* Quantity Stepper */}
        <div className="flex items-center border border-stone-200 rounded-lg bg-stone-50 overflow-hidden">
          <button
            id={`qty-decrease-${product.id}`}
            type="button"
            onClick={() => updateQuantity(product.id, quantity - 1)}
            disabled={quantity <= 1}
            className="p-1.5 sm:p-2 text-stone-600 hover:text-stone-900 hover:bg-stone-200 disabled:opacity-40 disabled:hover:bg-transparent transition cursor-pointer"
            aria-label="Decrease quantity"
          >
            <Minus className="w-3.5 h-3.5" />
          </button>
          <span
            id={`qty-count-${product.id}`}
            className="px-3 py-1 text-xs sm:text-sm font-bold text-stone-900 text-center min-w-[32px]"
          >
            {quantity}
          </span>
          <button
            id={`qty-increase-${product.id}`}
            type="button"
            onClick={() => updateQuantity(product.id, quantity + 1)}
            className="p-1.5 sm:p-2 text-stone-600 hover:text-stone-900 hover:bg-stone-200 transition cursor-pointer"
            aria-label="Increase quantity"
          >
            <Plus className="w-3.5 h-3.5" />
          </button>
        </div>

        {/* Item Total Price */}
        <div className="text-right min-w-[70px]">
          <span className="block text-sm sm:text-base font-extrabold text-stone-900">
            ${(product.price * quantity).toFixed(2)}
          </span>
          {quantity > 1 && (
            <span className="hidden sm:block text-[11px] text-stone-400">
              ${product.price} ea
            </span>
          )}
        </div>

        {/* Remove Button */}
        <button
          id={`cart-remove-btn-${product.id}`}
          type="button"
          onClick={() => removeFromCart(product.id)}
          className="p-2 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer"
          title="Remove item"
          aria-label={`Remove ${product.title} from cart`}
        >
          <Trash2 className="w-4 h-4" />
        </button>

      </div>
    </div>
  );
}
