import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
  title: 'Download WordPress Theme',
  description: 'Download the complete WordPress theme and commerce plugin as a ZIP archive.',
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" className="h-full">
      <body className="min-h-screen bg-stone-50 text-stone-900 flex flex-col font-sans antialiased selection:bg-stone-200">
        {children}
      </body>
    </html>
  );
}
