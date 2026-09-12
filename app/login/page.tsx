'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { Sparkles, Lock, Mail, User as UserIcon, ArrowRight, CheckCircle2 } from 'lucide-react';
import { useMarketplace } from '@/context/MarketplaceContext';

export default function LoginPage() {
  const router = useRouter();
  const { login, currentUser } = useMarketplace();

  const [mode, setMode] = useState<'login' | 'signup'>('login');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isLoading, setIsLoading] = useState(false);

  const validate = () => {
    const errs: Record<string, string> = {};

    if (mode === 'signup' && !name.trim()) {
      errs.name = 'Full name is required';
    }

    if (!email.trim()) {
      errs.email = 'Email address is required';
    } else if (!/\S+@\S+\.\S+/.test(email)) {
      errs.email = 'Please enter a valid email address';
    }

    if (!password) {
      errs.password = 'Password is required';
    } else if (password.length < 6) {
      errs.password = 'Password must be at least 6 characters';
    }

    if (mode === 'signup' && password !== confirmPassword) {
      errs.confirmPassword = 'Passwords do not match';
    }

    setErrors(errs);
    return Object.keys(errs).length === 0;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!validate()) return;

    setIsLoading(true);
    setTimeout(() => {
      login(email, mode === 'signup' ? name : undefined);
      setIsLoading(false);
      router.push('/account');
    }, 500);
  };

  const handleDemoSignIn = () => {
    setIsLoading(true);
    setTimeout(() => {
      login('demo.creator@marketplace.dev', 'Alex Mercer');
      setIsLoading(false);
      router.push('/account');
    }, 300);
  };

  return (
    <div id="auth-page-container" className="max-w-md mx-auto px-4 py-16 w-full flex-1 flex flex-col justify-center">
      <div className="bg-white rounded-3xl border border-stone-200 p-8 shadow-xs">
        
        {/* Brand Header */}
        <div className="text-center mb-8">
          <div className="w-12 h-12 rounded-2xl bg-stone-900 text-white flex items-center justify-center mx-auto mb-3 shadow-sm">
            <Sparkles className="w-6 h-6 text-amber-300" />
          </div>
          <h1 className="text-2xl font-black text-stone-900 tracking-tight">
            {mode === 'login' ? 'Welcome Back' : 'Create an Account'}
          </h1>
          <p className="text-xs text-stone-500 mt-1">
            {mode === 'login'
              ? 'Sign in to access your digital downloads and order invoices.'
              : 'Join to purchase digital assets, track orders, and receive updates.'}
          </p>
        </div>

        {/* Tab Switcher */}
        <div className="flex bg-stone-100 p-1 rounded-xl mb-6 text-xs font-semibold">
          <button
            id="tab-mode-login"
            type="button"
            onClick={() => {
              setMode('login');
              setErrors({});
            }}
            className={`flex-1 py-2 rounded-lg transition cursor-pointer ${
              mode === 'login'
                ? 'bg-white text-stone-900 shadow-2xs'
                : 'text-stone-500 hover:text-stone-800'
            }`}
          >
            Sign In
          </button>
          <button
            id="tab-mode-signup"
            type="button"
            onClick={() => {
              setMode('signup');
              setErrors({});
            }}
            className={`flex-1 py-2 rounded-lg transition cursor-pointer ${
              mode === 'signup'
                ? 'bg-white text-stone-900 shadow-2xs'
                : 'text-stone-500 hover:text-stone-800'
            }`}
          >
            Create Account
          </button>
        </div>

        {/* Form */}
        <form id="auth-form" onSubmit={handleSubmit} className="space-y-4">
          
          {mode === 'signup' && (
            <div>
              <label htmlFor="auth-name" className="block text-xs font-semibold text-stone-700 mb-1">
                Full Name
              </label>
              <div className="relative">
                <UserIcon className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" />
                <input
                  id="auth-name"
                  type="text"
                  placeholder="e.g. Alex Mercer"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className={`w-full pl-10 pr-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                    errors.name ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                  }`}
                />
              </div>
              {errors.name && <p className="text-xs text-rose-600 mt-1">{errors.name}</p>}
            </div>
          )}

          <div>
            <label htmlFor="auth-email" className="block text-xs font-semibold text-stone-700 mb-1">
              Email Address
            </label>
            <div className="relative">
              <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" />
              <input
                id="auth-email"
                type="email"
                placeholder="name@example.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className={`w-full pl-10 pr-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                  errors.email ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                }`}
              />
            </div>
            {errors.email && <p className="text-xs text-rose-600 mt-1">{errors.email}</p>}
          </div>

          <div>
            <div className="flex items-center justify-between mb-1">
              <label htmlFor="auth-password" className="block text-xs font-semibold text-stone-700">
                Password
              </label>
              {mode === 'login' && (
                <a
                  href="#forgot"
                  onClick={(e) => {
                    e.preventDefault();
                    alert('Password reset link simulated.');
                  }}
                  className="text-[11px] text-stone-500 hover:text-stone-900"
                >
                  Forgot password?
                </a>
              )}
            </div>
            <div className="relative">
              <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" />
              <input
                id="auth-password"
                type="password"
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className={`w-full pl-10 pr-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                  errors.password ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                }`}
              />
            </div>
            {errors.password && <p className="text-xs text-rose-600 mt-1">{errors.password}</p>}
          </div>

          {mode === 'signup' && (
            <div>
              <label htmlFor="auth-confirm-password" className="block text-xs font-semibold text-stone-700 mb-1">
                Confirm Password
              </label>
              <div className="relative">
                <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" />
                <input
                  id="auth-confirm-password"
                  type="password"
                  placeholder="••••••••"
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  className={`w-full pl-10 pr-3.5 py-2.5 bg-stone-50 border rounded-xl text-sm focus:outline-none focus:bg-white transition ${
                    errors.confirmPassword ? 'border-rose-400 bg-rose-50/50' : 'border-stone-200 focus:border-stone-400'
                  }`}
                />
              </div>
              {errors.confirmPassword && (
                <p className="text-xs text-rose-600 mt-1">{errors.confirmPassword}</p>
              )}
            </div>
          )}

          {/* Submit Button */}
          <button
            id="auth-submit-btn"
            type="submit"
            disabled={isLoading}
            className="w-full flex items-center justify-center gap-2 py-3 px-4 bg-stone-900 hover:bg-stone-800 active:bg-stone-950 disabled:opacity-50 text-white text-sm font-bold rounded-xl shadow-xs transition mt-2 cursor-pointer"
          >
            <span>{isLoading ? 'Processing...' : mode === 'login' ? 'Sign In' : 'Create Account'}</span>
            <ArrowRight className="w-4 h-4" />
          </button>
        </form>

        {/* Demo Fast Login */}
        <div className="mt-6 pt-5 border-t border-stone-100 text-center">
          <p className="text-xs text-stone-400 mb-3">Testing convenience:</p>
          <button
            id="demo-login-btn"
            type="button"
            onClick={handleDemoSignIn}
            className="w-full py-2 px-3 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-semibold rounded-lg transition border border-stone-200 cursor-pointer"
          >
            ⚡ 1-Click Fast Demo Sign In
          </button>
        </div>

      </div>
    </div>
  );
}
