'use client';

import React, { useState, use } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { useRouter } from 'next/navigation';
import {
  Star,
  ShoppingCart,
  Zap,
  CheckCircle2,
  DownloadCloud,
  ShieldCheck,
  Calendar,
  FileCode,
  HardDrive,
  ArrowLeft,
  Share2,
  Minus,
  Plus,
} from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';
import { ProductCard } from '@/components/ProductCard';

export default function ProductDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const resolvedParams = use(params);
  const router = useRouter();
  const { getProductById, addToCart, products, showToast } = useMarketplace();

  const product = getProductById(resolvedParams.id);
  const [selectedImage, setSelectedImage] = useState<string>(product?.image || '');
  const [quantity, setQuantity] = useState(1);

  if (!product) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center">
        <h2 className="text-2xl font-bold text-stone-900">Product Not Found</h2>
        <p className="text-sm text-stone-500 mt-2">
          The requested product does not exist in our catalog or has been moved.
        </p>
        <Link
          href="/products"
          className="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-stone-900 text-white text-xs font-semibold rounded-xl hover:bg-stone-800 transition"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Return to Catalog</span>
        </Link>
      </div>
    );
  }

  const allImages = [product.image, ...(product.additionalImages || [])];
  const activeImage = selectedImage || product.image;

  const handleAddToCart = () => {
    addToCart(product, quantity);
  };

  const handleBuyNow = () => {
    addToCart(product, quantity);
    router.push('/checkout');
  };

  const handleShare = () => {
    if (typeof window !== 'undefined') {
      navigator.clipboard?.writeText(window.location.href);
      showToast('Product link copied to clipboard');
    }
  };

  // Find related products in the same category
  const relatedProducts = products
    .filter((p) => p.category === product.category && p.id !== product.id)
    .slice(0, 3);

  return (
    <div id="product-detail-container" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex flex-col gap-12">
      
      {/* Breadcrumbs & Back Link */}
      <nav id="detail-breadcrumb" className="flex items-center justify-between text-xs text-stone-500">
        <div className="flex items-center gap-2">
          <Link href="/products" className="inline-flex items-center gap-1 hover:text-stone-900 transition">
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>All Products</span>
          </Link>
          <span>/</span>
          <Link
            href={`/products?category=${encodeURIComponent(product.category)}`}
            className="hover:text-stone-900 transition"
          >
            {product.category}
          </Link>
          <span>/</span>
          <span className="text-stone-800 font-medium truncate max-w-[200px] sm:max-w-xs">
            {product.title}
          </span>
        </div>

        <button
          type="button"
          onClick={handleShare}
          className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-stone-200 rounded-lg hover:bg-stone-50 text-stone-700 transition cursor-pointer"
        >
          <Share2 className="w-3.5 h-3.5" />
          <span>Share</span>
        </button>
      </nav>

      {/* Main Product Showcase Section */}
      <section className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
        
        {/* Left Column: Image Gallery */}
        <div className="lg:col-span-7 flex flex-col gap-4">
          {/* Main Large Image */}
          <div className="relative aspect-[16/10] w-full rounded-2xl overflow-hidden bg-stone-100 border border-stone-200 shadow-sm">
            <Image
              src={activeImage}
              alt={product.title}
              fill
              priority
              sizes="(max-width: 1024px) 100vw, 58vw"
              className="object-cover"
              referrerPolicy="no-referrer"
            />
            <div className="absolute top-4 left-4">
              <span className="px-3 py-1 text-xs font-semibold rounded-full bg-stone-900/90 text-white backdrop-blur-xs">
                {product.category}
              </span>
            </div>
          </div>

          {/* Thumbnails */}
          {allImages.length > 1 && (
            <div className="flex items-center gap-3 overflow-x-auto pb-1">
              {allImages.map((img, idx) => (
                <button
                  key={idx}
                  type="button"
                  onClick={() => setSelectedImage(img)}
                  className={`relative w-24 h-16 rounded-xl overflow-hidden border-2 shrink-0 transition cursor-pointer ${
                    activeImage === img ? 'border-stone-900 ring-2 ring-stone-900/20' : 'border-transparent opacity-70 hover:opacity-100'
                  }`}
                >
                  <Image
                    src={img}
                    alt={`${product.title} preview ${idx + 1}`}
                    fill
                    sizes="96px"
                    className="object-cover"
                    referrerPolicy="no-referrer"
                  />
                </button>
              ))}
            </div>
          )}

          {/* Guarantee Badges */}
          <div className="grid grid-cols-3 gap-3 p-4 bg-white rounded-xl border border-stone-200 text-center text-xs text-stone-600 mt-2">
            <div className="flex flex-col items-center gap-1.5 p-2">
              <DownloadCloud className="w-5 h-5 text-stone-700" />
              <span className="font-semibold text-stone-900">Instant Access</span>
              <span className="text-[11px] text-stone-400">Direct file link</span>
            </div>
            <div className="flex flex-col items-center gap-1.5 p-2 border-x border-stone-100">
              <ShieldCheck className="w-5 h-5 text-emerald-600" />
              <span className="font-semibold text-stone-900">Commercial Use</span>
              <span className="text-[11px] text-stone-400">Royalty-free license</span>
            </div>
            <div className="flex flex-col items-center gap-1.5 p-2">
              <Calendar className="w-5 h-5 text-stone-700" />
              <span className="font-semibold text-stone-900">Updated {product.lastUpdated}</span>
              <span className="text-[11px] text-stone-400">Free updates included</span>
            </div>
          </div>
        </div>

        {/* Right Column: Pricing & Purchase Card */}
        <div className="lg:col-span-5 flex flex-col gap-6">
          <div className="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-xs space-y-6">
            
            {/* Author info & Rating */}
            <div className="flex items-center justify-between gap-2 text-xs text-stone-500 border-b border-stone-100 pb-4">
              <div className="flex items-center gap-2">
                <div className="relative w-7 h-7 rounded-full overflow-hidden bg-stone-200">
                  <Image
                    src={product.author.avatar}
                    alt={product.author.name}
                    fill
                    sizes="28px"
                    className="object-cover"
                    referrerPolicy="no-referrer"
                  />
                </div>
                <span>By <strong className="text-stone-900 font-semibold">{product.author.name}</strong></span>
              </div>

              <div className="flex items-center gap-1.5 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/50">
                <Star className="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                <span className="font-bold text-stone-900 text-xs">{product.rating}</span>
                <span className="text-stone-500 text-[11px]">({product.reviewCount} reviews)</span>
              </div>
            </div>

            {/* Title */}
            <h1 className="text-2xl sm:text-3xl font-extrabold text-stone-900 leading-tight">
              {product.title}
            </h1>

            {/* Price Tag */}
            <div className="flex items-baseline gap-3">
              <span className="text-3xl sm:text-4xl font-black text-stone-900">
                ${product.price}
              </span>
              {product.originalPrice && (
                <span className="text-sm sm:text-base text-stone-400 line-through">
                  ${product.originalPrice}
                </span>
              )}
              {product.originalPrice && (
                <span className="px-2 py-0.5 text-xs font-bold text-emerald-700 bg-emerald-100 rounded-md">
                  Save ${(product.originalPrice - product.price).toFixed(0)}
                </span>
              )}
            </div>

            {/* Quantity Selector */}
            <div className="flex items-center justify-between p-3 bg-stone-50 rounded-xl border border-stone-200">
              <span className="text-xs font-semibold text-stone-700">Licenses / Seats:</span>
              <div className="flex items-center border border-stone-300 rounded-lg bg-white overflow-hidden">
                <button
                  type="button"
                  onClick={() => setQuantity(Math.max(1, quantity - 1))}
                  disabled={quantity <= 1}
                  className="p-1.5 text-stone-600 hover:text-stone-900 disabled:opacity-30 cursor-pointer"
                  aria-label="Decrease license count"
                >
                  <Minus className="w-3.5 h-3.5" />
                </button>
                <span className="px-3 py-0.5 text-xs font-bold text-stone-900">
                  {quantity}
                </span>
                <button
                  type="button"
                  onClick={() => setQuantity(quantity + 1)}
                  className="p-1.5 text-stone-600 hover:text-stone-900 cursor-pointer"
                  aria-label="Increase license count"
                >
                  <Plus className="w-3.5 h-3.5" />
                </button>
              </div>
            </div>

            {/* CTA Buttons */}
            <div className="space-y-3 pt-2">
              <button
                id="product-buy-now-btn"
                type="button"
                onClick={handleBuyNow}
                className="w-full flex items-center justify-center gap-2 py-3.5 px-6 bg-stone-900 hover:bg-stone-800 active:bg-stone-950 text-white font-bold rounded-xl shadow-xs transition cursor-pointer text-sm"
              >
                <Zap className="w-4 h-4 text-amber-400 fill-amber-400" />
                <span>Buy Now — ${(product.price * quantity).toFixed(2)}</span>
              </button>

              <button
                id="product-add-to-cart-btn"
                type="button"
                onClick={handleAddToCart}
                className="w-full flex items-center justify-center gap-2 py-3 px-6 bg-stone-100 hover:bg-stone-200 active:bg-stone-300 text-stone-900 font-semibold rounded-xl border border-stone-200 transition cursor-pointer text-sm"
              >
                <ShoppingCart className="w-4 h-4 text-stone-700" />
                <span>Add to Cart</span>
              </button>
            </div>

            {/* File & Technical Meta */}
            <div className="pt-4 border-t border-stone-100 space-y-2.5 text-xs">
              <div className="flex items-center justify-between text-stone-600">
                <span className="flex items-center gap-1.5 text-stone-500">
                  <FileCode className="w-3.5 h-3.5" /> Format:
                </span>
                <span className="font-semibold text-stone-900">{product.fileFormat}</span>
              </div>
              <div className="flex items-center justify-between text-stone-600">
                <span className="flex items-center gap-1.5 text-stone-500">
                  <HardDrive className="w-3.5 h-3.5" /> Download Size:
                </span>
                <span className="font-semibold text-stone-900">{product.fileSize}</span>
              </div>
              <div className="flex items-center justify-between text-stone-600">
                <span className="flex items-center gap-1.5 text-stone-500">
                  <ShieldCheck className="w-3.5 h-3.5" /> Licensing:
                </span>
                <span className="font-semibold text-stone-900">Commercial & Personal</span>
              </div>
            </div>

          </div>
        </div>

      </section>

      {/* Description & Feature Specifications */}
      <section className="bg-white rounded-2xl border border-stone-200 p-6 sm:p-10 shadow-2xs space-y-8">
        <div>
          <h2 className="text-xl font-bold text-stone-900 mb-4">
            Product Overview
          </h2>
          <p className="text-sm sm:text-base text-stone-600 leading-relaxed max-w-4xl whitespace-pre-line">
            {product.longDescription}
          </p>
        </div>

        {/* Feature list */}
        <div className="border-t border-stone-100 pt-6">
          <h3 className="text-base font-bold text-stone-900 mb-4">
            Key Inclusions & Highlights
          </h3>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            {product.features.map((feature, idx) => (
              <div key={idx} className="flex items-start gap-2.5 text-sm text-stone-700">
                <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                <span>{feature}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Tags */}
        <div className="border-t border-stone-100 pt-6 flex items-center gap-2 flex-wrap">
          <span className="text-xs font-semibold text-stone-500">Tags:</span>
          {product.tags.map((tag) => (
            <Link
              key={tag}
              href={`/products?q=${encodeURIComponent(tag)}`}
              className="px-3 py-1 text-xs font-medium bg-stone-100 text-stone-700 hover:bg-stone-200 rounded-md transition"
            >
              #{tag}
            </Link>
          ))}
        </div>
      </section>

      {/* Related Products */}
      {relatedProducts.length > 0 && (
        <section id="related-products-section" className="space-y-6 pt-4">
          <div className="flex items-center justify-between">
            <h2 className="text-2xl font-bold text-stone-900">
              More in {product.category}
            </h2>
            <Link
              href={`/products?category=${encodeURIComponent(product.category)}`}
              className="text-xs font-semibold text-stone-700 hover:text-stone-900"
            >
              View category →
            </Link>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {relatedProducts.map((rel) => (
              <ProductCard key={rel.id} product={rel} />
            ))}
          </div>
        </section>
      )}

    </div>
  );
}
