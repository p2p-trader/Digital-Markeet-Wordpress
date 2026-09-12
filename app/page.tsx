'use client';

import { useState } from 'react';
import { Download, Loader2, AlertCircle, CheckCircle2 } from 'lucide-react';

export default function DownloadPage() {
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const handleDownload = async () => {
    setStatus('loading');
    setErrorMessage(null);

    try {
      const response = await fetch('/api/download');

      if (!response.ok) {
        let errorText = `Server responded with status ${response.status}`;
        try {
          const json = await response.json();
          if (json?.error) {
            errorText = json.error;
          }
        } catch {
          // If not JSON, use status text
          errorText = response.statusText || errorText;
        }
        throw new Error(errorText);
      }

      // Convert response to blob
      const blob = await response.blob();
      if (blob.size === 0) {
        throw new Error('Received an empty archive from the server.');
      }

      // Trigger browser file download
      const downloadUrl = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = 'wordpress-theme.zip';
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(downloadUrl);

      setStatus('success');
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'An unexpected error occurred while downloading the zip file.';
      setErrorMessage(msg);
      setStatus('error');
    }
  };

  return (
    <main className="min-h-screen flex items-center justify-center p-6 sm:p-10">
      <div
        id="download-card"
        className="w-full max-w-md bg-white border border-stone-200 rounded-2xl p-8 sm:p-10 shadow-sm text-center"
      >
        <div className="w-14 h-14 mx-auto mb-6 bg-stone-100 border border-stone-200 rounded-xl flex items-center justify-center text-stone-700">
          <Download className="w-6 h-6" />
        </div>

        <h1
          id="download-heading"
          className="text-2xl sm:text-3xl font-bold tracking-tight text-stone-900 mb-3"
        >
          Download WordPress Theme
        </h1>

        <p className="text-sm text-stone-600 mb-8 leading-relaxed">
          Package and download the complete WordPress theme along with the bundled Digital Marketplace Commerce plugin as a single ZIP archive.
        </p>

        {status === 'error' && errorMessage && (
          <div
            id="download-error-box"
            role="alert"
            className="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-left flex items-start gap-3"
          >
            <AlertCircle className="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
            <div className="text-xs text-red-700 leading-relaxed">
              <span className="font-semibold block mb-0.5">Download Failed:</span>
              {errorMessage}
            </div>
          </div>
        )}

        {status === 'success' && (
          <div
            id="download-success-box"
            className="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-left flex items-start gap-3"
          >
            <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
            <div className="text-xs text-emerald-700 leading-relaxed">
              <span className="font-semibold block mb-0.5">Download Started!</span>
              Your browser is downloading <strong className="font-mono">wordpress-theme.zip</strong>. If it did not start automatically, click the button below to retry.
            </div>
          </div>
        )}

        <button
          id="download-zip-btn"
          type="button"
          onClick={handleDownload}
          disabled={status === 'loading'}
          className="w-full py-3.5 px-7 bg-stone-900 hover:bg-stone-800 active:bg-stone-950 text-white font-medium text-base rounded-xl transition-colors shadow-sm disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2.5 cursor-pointer"
        >
          {status === 'loading' ? (
            <>
              <Loader2 className="w-5 h-5 animate-spin" />
              <span>Zipping Files...</span>
            </>
          ) : (
            <>
              <Download className="w-5 h-5" />
              <span>Download ZIP</span>
            </>
          )}
        </button>
      </div>
    </main>
  );
}
