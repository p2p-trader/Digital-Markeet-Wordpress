# Digital Marketplace WordPress Theme & Commerce Engine

A modern, high-performance WordPress theme and standalone e-commerce plugin for digital assets, UI kits, code boilerplates, fonts, and creative resources. Designed to mirror the React/Next.js Digital Marketplace application with semantic HTML5, WordPress core authentication, dynamic custom post types, and a self-contained cryptocurrency commerce plugin that works **without** requiring WooCommerce or any third-party plugins.

---

## 📁 Project Directory Structure

```text
wordpress/
├── style.css                          # Core theme definition and required WordPress header
├── functions.php                      # Theme setup, assets enqueueing, CPT registration, meta boxes
├── header.php                         # Global navigation, search bar, dynamic cart badge, user status
├── footer.php                         # Value propositions, category links, trust badges, wp_footer()
├── front-page.php                     # Home page with hero, categories, dynamic featured products
├── archive-product.php                # Products catalog with categories, search, and pagination
├── single-product.php                 # Product detail page with gallery thumbnails, price, specs, DMC buy actions
├── page-cart.php                      # Cart template executing [dmc_cart] shortcode dynamically
├── page-checkout.php                  # Checkout template executing [dmc_checkout] shortcode dynamically
├── page-login.php                     # Authentication template powered by wp_login_form()
├── page-account.php                   # Customer dashboard querying real dmc_order records
├── index.php                          # Fallback blog/post archive template
├── assets/
│   ├── css/theme.css                  # Clean styling preserving the exact React/Tailwind visual design
│   └── js/marketplace.js             # Client interactivity: gallery previews, mobile nav
└── digital-marketplace-commerce/      # Standalone E-Commerce Plugin (NO WOOCOMMERCE REQUIRED!)
    ├── digital-marketplace-commerce.php  # Main plugin header & loader
    ├── includes/
    │   ├── class-dmc-cart.php         # Tamper-resistant signed cookie cart manager
    │   ├── class-dmc-post-type.php    # dmc_order CPT, admin columns, metabox, status filter
    │   ├── class-dmc-ajax.php         # AJAX handlers for add, update qty, remove item
    │   ├── class-dmc-checkout.php     # [dmc_cart], [dmc_checkout], order creation & wp_mail
    │   └── class-dmc-settings.php     # Settings > Digital Marketplace (wallets, email, stale flag)
    └── assets/
        ├── js/dmc-commerce.js         # Client AJAX cart interactions and badge updates
        └── css/dmc-commerce.css       # Clean component styling
```

---

## 🚀 Installation & Setup

### Step 1: Install & Activate the Theme (Automatic Plugin Installation)
1. Copy the `wordpress` folder into your WordPress installation at:
   `wp-content/themes/digital-marketplace/`
   *(Ensure the bundled `digital-marketplace-commerce/` folder remains inside the theme folder).*
2. In your WordPress Admin Dashboard, navigate to **Appearance > Themes** and click **Activate** on **Digital Marketplace Theme**.
3. **Auto-Install Magic**: Upon theme activation, `functions.php` automatically copies the bundled `digital-marketplace-commerce` folder into `wp-content/plugins/` using the secure WordPress Filesystem API and activates it immediately.
4. If your server environment prevents automatic file copying (such as strict file ownership or read-only `wp-content/plugins/`), an informational admin notice will appear in `wp-admin` with instructions to copy the folder manually.

### Step 2: Configure Crypto Wallets
1. Go to **Settings > Digital Marketplace** in your WordPress admin menu.
2. Enter your real cryptocurrency wallet addresses:
   - **Bitcoin (BTC)** address
   - **Ethereum (ETH)** address
   - **Tether (USDT)** address
3. Customize the confirmation email template text if desired.
4. Set the stale order threshold (default: 24 hours).
5. Click **Save Commerce Settings**.

### Step 4: Create Core Pages
Create the following pages under **Pages > Add New**:
- **Cart**: Assign the "Cart Page" template. (Renders `[dmc_cart]` with dynamic line items).
- **Checkout**: Assign the "Checkout Page" template. (Renders `[dmc_checkout]` with crypto payment flow and confirmation).
- **My Account**: Assign the "Account Dashboard Page" template. (Queries `dmc_order` posts for the logged-in customer).
- **Sign In**: Assign the "Login / Signup Page" template. (Uses `wp_login_form()`).

### Step 5: Managing Orders in WP-Admin
1. When a buyer submits an order, it is stored in `wp-admin` under **Orders (DMC)**.
2. The order begins with status **Awaiting Payment**.
3. View the single order screen to inspect the customer name, email, items purchased, and crypto address provided.
4. After verifying the blockchain transaction in your wallet, simply change the status dropdown to **Paid - Processing** or **Completed** and click **Update**.
5. The Orders list table includes a status filter dropdown and an informational **May Be Stale** badge if an unpaid order exceeds your configured hours.
