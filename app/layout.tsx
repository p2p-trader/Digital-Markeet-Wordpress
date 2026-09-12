import type { Metadata } from 'next';
import './globals.css';
import { MarketplaceProvider } from '@/context/MarketplaceContext';
import { Navbar } from '@/components/Navbar';
import { Footer } from '@/components/Footer';
import { Toast } from '@/components/Toast';

export const metadata: Metadata = {
  title: 'Digital Marketplace',
  description: 'A digital marketplace where users can browse products, view details, manage cart, and purchase items.',
  openGraph: {
    title: 'Digital Marketplace',
    description: 'A digital marketplace where users can browse products, view details, manage cart, and purchase items.',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Digital Marketplace',
    description: 'A digital marketplace where users can browse products, view details, manage cart, and purchase items.',
  },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className="h-full scroll-smooth">
      <body className="min-h-screen bg-stone-50 text-stone-900 flex flex-col font-sans antialiased" suppressHydrationWarning>
        <MarketplaceProvider>
          <Navbar />
          <main className="flex-1 flex flex-col">
            {children}
          </main>
          <Footer />
          <Toast />
        </MarketplaceProvider>
      </body>
    </html>
  );
}
