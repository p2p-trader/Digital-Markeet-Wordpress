# Digital Marketplace WordPress Theme

A modern, high-performance WordPress theme for digital assets, UI kits, code boilerplates, fonts, and creative resources. Designed to mirror the React/Next.js Digital Marketplace application with semantic HTML5, WordPress core authentication, dynamic custom post types, and full WooCommerce compatibility.

---

## 📁 Theme Directory Structure

```text
wordpress/
├── style.css               # Core theme definition and required WordPress header
├── functions.php           # Theme setup, assets enqueueing, CPT registration, meta boxes
├── header.php              # Global navigation, search bar, dynamic cart badge, user status
├── footer.php              # Value propositions, category links, trust badges, wp_footer()
├── front-page.php          # Home page with hero, categories, dynamic featured products
├── archive-product.php     # Products catalog with categories, search, and pagination
├── single-product.php      # Product detail page with gallery thumbnails, price, specs, buy actions
├── page-cart.php           # Cart template with [woocommerce_cart] shortcode support
├── page-checkout.php       # Checkout template with [woocommerce_checkout] shortcode support
├── page-login.php          # Authentication template powered by wp_login_form()
├── page-account.php        # Customer dashboard with order history, downloads, and profile tabs
├── index.php               # Fallback blog/post archive template
└── assets/
    ├── css/
    │   └── theme.css       # Clean styling preserving the exact React/Tailwind visual design
    └── js/
        └── marketplace.js  # Client interactivity: gallery previews, quantity steppers, coupons
```

---

## 🚀 Installation & Setup

1. **Copy the theme folder**:
   Copy the `wordpress` folder into your WordPress installation at:
   `wp-content/themes/digital-marketplace/`

2. **Activate the theme**:
   In your WordPress Admin Dashboard, navigate to **Appearance > Themes** and click **Activate** on **Digital Marketplace Theme**.

3. **Set Up Core Pages**:
   Create the following pages in **Pages > Add New**:
   - **Cart**: Assign the "Cart Page" template (or use WooCommerce default).
   - **Checkout**: Assign the "Checkout Page" template (or use WooCommerce default).
   - **My Account**: Assign the "Account Dashboard Page" template.
   - **Sign In**: Assign the "Login / Signup Page" template.

4. **Set Front Page**:
   Go to **Settings > Reading** and choose **A static page**, selecting your Home page. `front-page.php` will automatically render the home experience.

---

## 🛒 WooCommerce Integration Guide

This theme is intentionally engineered with a hybrid architecture:
- **Standalone Mode (No plugins required)**: Uses the built-in `product` Custom Post Type with native meta boxes (Price, Format, Size, Featured flag, Reviews), mock interactive cart, and WordPress core authentication.
- **Production Commerce Mode (Recommended)**: Install and activate the official **WooCommerce** plugin.

When WooCommerce is activated:
1. `page-cart.php` automatically renders WooCommerce's secure server-side session cart via `[woocommerce_cart]`.
2. `page-checkout.php` automatically mounts PCI-compliant payment gateways (Stripe, PayPal) via `[woocommerce_checkout]`.
3. `single-product.php` hooks into WooCommerce's `woocommerce_template_single_add_to_cart()`.
4. `page-account.php` dynamically queries orders via `wc_get_orders()` and links directly to customer digital downloads and order receipts.
