# WowLady E-Commerce Store

A full-featured e-commerce platform built with PHP, MySQL, and modern web technologies. Perfect for women's fashion, accessories, and lifestyle products.

## ✨ Features

### 🛍️ Product Management
- **Admin Dashboard** - Add, edit, and manage products
- **Rich Categories** - 50+ organized product categories
- **Product Images** - AVIF, WebP, JPEG, PNG, GIF support
- **Inventory Management** - Track stock levels

### 💳 Shopping Features
- **Shopping Cart** - Add/remove items, update quantities
- **Wishlist** - Save favorite items with heart button (white/red toggle)
- **Direct Purchase** - Buy now functionality
- **Order Management** - View and track orders

### 👤 User Management
- **User Registration & Login** - Secure authentication
- **User Profile** - Manage account details
- **Order History** - View past purchases
- **Admin Panel** - Exclusive admin features

### 💝 Additional Features
- **Search Functionality** - Find products by name or description
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Modern UI** - Clean and intuitive interface
- **Security** - Prepared statements, session management, password hashing

## 📁 Project Structure

```
ecommerce/
├── admin/                 # Admin panel
│   ├── add-product.php   # Add new products
│   ├── manage-products.php
│   ├── save-product.php
│   └── upload/           # Product images
├── api/                   # API endpoints
│   ├── add-to-cart.php
│   ├── buy-product.php
│   ├── toggle-wishlist.php
│   ├── login.php
│   ├── register.php
│   └── ...
├── public/               # Public pages
│   ├── index.php         # Home page
│   ├── product.php       # Product detail
│   ├── cart.php
│   ├── checkout.php
│   ├── wishlist.php
│   ├── orders.php
│   ├── login.php
│   └── register.php
├── config/              # Configuration
│   ├── db.php          # Database connection
│   └── session.php     # Session management
└── assets/             # Static files
    ├── css/
    └── images/
```

## 🚀 Installation

### Prerequisites
- XAMPP (PHP 7.4+, MySQL 5.7+)
- Git

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/bhagvatimakne/ecommerce.git
   cd ecommerce
   ```

2. **Setup Database**
   - Open phpMyAdmin
   - Create database `ecommerce_db`
   - Import the SQL schema (if provided)

3. **Configure Database**
   - Edit `config/db.php` with your database credentials
   ```php
   $conn = new mysqli("localhost", "root", "", "ecommerce_db");
   ```

4. **Run on XAMPP**
   - Place folder in `htdocs` directory
   - Start Apache and MySQL
   - Open `http://localhost/ecommerce/public/index.php`

## 🔐 Default Admin Account
- **Email**: admin@gmail.com
- **Password**: (Configure in database)

## 📱 Pages Overview

| Page | URL | Description |
|------|-----|-------------|
| Home | `/public/index.php` | Product listing and browse |
| Product Detail | `/public/product.php?id=X` | Single product view |
| Shopping Cart | `/public/cart.php` | View and manage cart items |
| Checkout | `/public/checkout.php` | Complete purchase |
| Wishlist | `/public/wishlist.php` | Saved items (white/red hearts) |
| Orders | `/public/orders.php` | Order history |
| Login | `/public/login.php` | User authentication |
| Register | `/public/register.php` | Create new account |

## 🎨 Features Implemented

✅ Responsive shopping cart with add/remove items  
✅ Wishlist with real-time heart button (white → red)  
✅ User authentication and authorization  
✅ Product search and filtering  
✅ Order management system  
✅ Admin product management  
✅ Image upload and optimization  
✅ Session management  
✅ Payment checkout flow  

## 🔄 Recent Updates

- Added comprehensive product categories (50+)
- Implemented wishlist toggle with visual feedback
- Removed unnecessary debug files
- Added .gitignore for security
- Optimized code by removing duplicate endpoints

## 📞 Support

For issues or questions, please open an issue on GitHub.

## 📄 License

This project is open source and available under the MIT License.

---

**Created with ❤️ by Bhagvati Makne**
