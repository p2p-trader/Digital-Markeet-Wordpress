'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import { Product, CartItem, Order, UserProfile } from '@/types/marketplace';
import { MOCK_PRODUCTS, INITIAL_MOCK_ORDERS } from '@/data/mock-products';

interface MarketplaceContextType {
  products: Product[];
  cart: CartItem[];
  orders: Order[];
  currentUser: UserProfile | null;
  toast: string | null;
  addToCart: (product: Product, quantity?: number) => void;
  removeFromCart: (productId: string) => void;
  updateQuantity: (productId: string, quantity: number) => void;
  clearCart: () => void;
  cartCount: number;
  cartSubtotal: number;
  cartTax: number;
  cartTotal: number;
  placeOrder: (customerData: {
    fullName: string;
    email: string;
    address: string;
    city: string;
    zipCode: string;
    paymentMethod: string;
  }) => Order;
  login: (email: string, name?: string) => void;
  logout: () => void;
  showToast: (message: string) => void;
  getProductById: (id: string) => Product | undefined;
}

const MarketplaceContext = createContext<MarketplaceContextType | undefined>(undefined);

const DEFAULT_USER: UserProfile = {
  id: 'usr-1',
  name: 'Syed Bakhtawar Shah',
  email: 'info.syedbakhtawarshah@gmail.com',
  joinedDate: 'August 2026',
  avatarUrl: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
};

export function MarketplaceProvider({ children }: { children: React.ReactNode }) {
  const [products] = useState<Product[]>(MOCK_PRODUCTS);
  const [cart, setCart] = useState<CartItem[]>(() => {
    if (typeof window !== 'undefined') {
      try {
        const storedCart = localStorage.getItem('marketplace_cart');
        if (storedCart) return JSON.parse(storedCart);
      } catch {
        // Ignore localStorage error
      }
    }
    return [];
  });

  const [orders, setOrders] = useState<Order[]>(() => {
    if (typeof window !== 'undefined') {
      try {
        const storedOrders = localStorage.getItem('marketplace_orders');
        if (storedOrders) return JSON.parse(storedOrders);
      } catch {
        // Ignore localStorage error
      }
    }
    return INITIAL_MOCK_ORDERS;
  });

  const [currentUser, setCurrentUser] = useState<UserProfile | null>(() => {
    if (typeof window !== 'undefined') {
      try {
        const storedUser = localStorage.getItem('marketplace_user');
        if (storedUser) return JSON.parse(storedUser);
      } catch {
        // Ignore localStorage error
      }
    }
    return DEFAULT_USER;
  });

  const [toast, setToast] = useState<string | null>(null);

  // Sync cart to localStorage
  useEffect(() => {
    if (typeof window === 'undefined') return;
    try {
      localStorage.setItem('marketplace_cart', JSON.stringify(cart));
    } catch (e) {
      console.warn('Could not save cart to localStorage', e);
    }
  }, [cart]);

  // Sync orders to localStorage
  useEffect(() => {
    if (typeof window === 'undefined') return;
    try {
      localStorage.setItem('marketplace_orders', JSON.stringify(orders));
    } catch (e) {
      console.warn('Could not save orders to localStorage', e);
    }
  }, [orders]);

  // Sync user to localStorage
  useEffect(() => {
    if (typeof window === 'undefined') return;
    try {
      if (currentUser) {
        localStorage.setItem('marketplace_user', JSON.stringify(currentUser));
      } else {
        localStorage.removeItem('marketplace_user');
      }
    } catch (e) {
      console.warn('Could not sync user to localStorage', e);
    }
  }, [currentUser]);

  const showToast = (message: string) => {
    setToast(message);
    setTimeout(() => {
      setToast((prev) => (prev === message ? null : prev));
    }, 3200);
  };

  const addToCart = (product: Product, quantity = 1) => {
    setCart((prevCart) => {
      const existing = prevCart.find((item) => item.product.id === product.id);
      if (existing) {
        return prevCart.map((item) =>
          item.product.id === product.id
            ? { ...item, quantity: item.quantity + quantity }
            : item
        );
      }
      return [...prevCart, { product, quantity }];
    });
    showToast(`Added "${product.title}" to cart`);
  };

  const removeFromCart = (productId: string) => {
    const item = cart.find((i) => i.product.id === productId);
    setCart((prevCart) => prevCart.filter((i) => i.product.id !== productId));
    if (item) {
      showToast(`Removed "${item.product.title}" from cart`);
    }
  };

  const updateQuantity = (productId: string, quantity: number) => {
    if (quantity <= 0) {
      removeFromCart(productId);
      return;
    }
    setCart((prevCart) =>
      prevCart.map((item) =>
        item.product.id === productId ? { ...item, quantity } : item
      )
    );
  };

  const clearCart = () => {
    setCart([]);
  };

  const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
  const cartSubtotal = cart.reduce(
    (total, item) => total + item.product.price * item.quantity,
    0
  );
  const cartTax = Number((cartSubtotal * 0.08).toFixed(2));
  const cartTotal = Number((cartSubtotal + cartTax).toFixed(2));

  const placeOrder = (customerData: {
    fullName: string;
    email: string;
    address: string;
    city: string;
    zipCode: string;
    paymentMethod: string;
  }): Order => {
    const newOrder: Order = {
      id: `ORD-${Math.floor(10000 + Math.random() * 90000)}`,
      createdAt: new Date().toISOString(),
      items: cart.map((item) => ({
        productId: item.product.id,
        title: item.product.title,
        price: item.product.price,
        quantity: item.quantity,
        image: item.product.image,
      })),
      subtotal: cartSubtotal,
      tax: cartTax,
      discount: 0,
      total: cartTotal,
      customer: customerData,
      paymentMethod: customerData.paymentMethod,
      status: 'Completed',
    };

    setOrders((prev) => [newOrder, ...prev]);
    clearCart();
    return newOrder;
  };

  const login = (email: string, name?: string) => {
    const formattedName = name || email.split('@')[0].replace('.', ' ');
    const user: UserProfile = {
      id: `usr-${Date.now()}`,
      name: formattedName.charAt(0).toUpperCase() + formattedName.slice(1),
      email,
      joinedDate: 'Today',
      avatarUrl: `https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80`,
    };
    setCurrentUser(user);
    showToast(`Signed in as ${user.name}`);
  };

  const logout = () => {
    setCurrentUser(null);
    showToast('Signed out of marketplace');
  };

  const getProductById = (id: string): Product | undefined => {
    return products.find((p) => p.id === id);
  };

  return (
    <MarketplaceContext.Provider
      value={{
        products,
        cart,
        orders,
        currentUser,
        toast,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        cartCount,
        cartSubtotal,
        cartTax,
        cartTotal,
        placeOrder,
        login,
        logout,
        showToast,
        getProductById,
      }}
    >
      {children}
    </MarketplaceContext.Provider>
  );
}

export function useMarketplace() {
  const context = useContext(MarketplaceContext);
  if (!context) {
    throw new Error('useMarketplace must be used within a MarketplaceProvider');
  }
  return context;
}
