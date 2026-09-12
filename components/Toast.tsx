'use client';

import React from 'react';
import { CheckCircle, X } from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';

export function Toast() {
  const { toast } = useMarketplace();

  if (!toast) return null;

  return (
    <div
      id="marketplace-toast-alert"
      className="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 bg-stone-900 text-white rounded-xl shadow-xl border border-stone-800 text-sm animate-bounce-short"
      role="status"
      aria-live="polite"
    >
      <CheckCircle className="w-4 h-4 text-emerald-400 shrink-0" />
      <span className="font-medium text-xs sm:text-sm">{toast}</span>
    </div>
  );
}
