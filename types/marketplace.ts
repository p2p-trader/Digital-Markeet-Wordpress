export interface Product {
  id: string;
  title: string;
  category: string;
  price: number;
  originalPrice?: number;
  description: string;
  longDescription: string;
  image: string;
  additionalImages?: string[];
  rating: number;
  reviewCount: number;
  author: {
    name: string;
    avatar: string;
  };
  tags: string[];
  features: string[];
  fileFormat: string;
  fileSize: string;
  isFeatured?: boolean;
  salesCount: number;
  lastUpdated: string;
}

export interface CartItem {
  product: Product;
  quantity: number;
}

export interface OrderItem {
  productId: string;
  title: string;
  price: number;
  quantity: number;
  image: string;
}

export interface Order {
  id: string;
  createdAt: string;
  items: OrderItem[];
  subtotal: number;
  tax: number;
  discount: number;
  total: number;
  customer: {
    fullName: string;
    email: string;
    address: string;
    city: string;
    zipCode: string;
  };
  paymentMethod: string;
  status: 'Completed' | 'Processing' | 'Delivered';
}

export interface UserProfile {
  id: string;
  name: string;
  email: string;
  joinedDate: string;
  avatarUrl: string;
}
