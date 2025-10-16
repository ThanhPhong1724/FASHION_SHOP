<!-- 16f5c03b-133a-461e-afe5-f0bd259dc3e3 1d60cdd8-c30d-4842-b233-a056a5891dae -->
# Kế hoạch Chi tiết: Website Bán Thời Trang Laravel 12

## 1. KIẾN TRÚC & STACK CÔNG NGHỆ

### 1.1 Tech Stack
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend User**: Blade Templates + TailwindCSS + Alpine.js
- **Admin Panel**: Filament v3 (modern, component-based, miễn phí)
- **Database**: MySQL/PostgreSQL (production), SQLite (local dev)
- **Storage**: Local disk với symlink, hỗ trợ S3 (optional)
- **Auth**: Laravel Breeze (Blade stack)
- **Cache**: Redis (production), File (local)
- **Queue**: Database driver (đơn giản nhất)

### 1.2 Packages Chính

| Package | Version | Mục đích | Lý do chọn |
|---------|---------|----------|------------|
| `filament/filament` | ^3.2 | Admin panel | Miễn phí, modern UI, CRUD tự động, nhiều widgets, cộng đồng lớn |
| `spatie/laravel-permission` | ^6.0 | RBAC | Chuẩn industry, dễ dùng, tích hợp Filament tốt |
| `spatie/laravel-medialibrary` | ^11.0 | Quản lý media | Tự động thumbnail, responsive images, tích hợp model |
| `intervention/image` | ^3.0 | Xử lý ảnh | Resize, crop, optimize, PHP 8.2 compatible |
| `gloudemans/shoppingcart` | ^4.0 | Giỏ hàng session | API đơn giản, lưu session/database, tính toán tax |
| `laravel/breeze` | ^2.0 | Auth scaffold | Nhẹ, đơn giản, Blade + Tailwind sẵn |
| `barryvdh/laravel-debugbar` | ^3.13 | Debug (dev) | Query monitoring, N+1 detection |
| `spatie/laravel-query-builder` | ^5.8 | API filtering | Sort, filter, include relations dễ dàng |

### 1.3 Kiến trúc phân lớp

```
┌─────────────────────────────────────────┐
│         Routes (web.php, admin.php)      │
├─────────────────────────────────────────┤
│  Controllers (Resource, Invokable)       │
├─────────────────────────────────────────┤
│  Form Requests (Validation)              │
├─────────────────────────────────────────┤
│  Services (Business Logic)               │ ← Cart, Order, Payment
├─────────────────────────────────────────┤
│  Repositories (optional, complex query)  │
├─────────────────────────────────────────┤
│  Models (Eloquent + Relations)           │
├─────────────────────────────────────────┤
│  Database (Migrations + Seeders)         │
└─────────────────────────────────────────┘
```

---

## 2. ENTITY RELATIONSHIP DIAGRAM (ERD)

### 2.1 Danh sách Tables

#### **Auth & RBAC**
- `users` (id, name, email, password, email_verified_at, avatar, phone, timestamps)
- `roles` (id, name, guard_name, timestamps) - Spatie
- `permissions` (id, name, guard_name, timestamps) - Spatie
- `model_has_roles` (role_id, model_type, model_id) - Pivot
- `model_has_permissions` - Pivot
- `role_has_permissions` - Pivot

#### **Catalog**
- `categories` (id, parent_id, name, slug, description, image, position, is_active, timestamps)
  - Index: `slug`, `parent_id`, `is_active`
- `brands` (id, name, slug, logo, description, is_active, timestamps)
  - Index: `slug`, `is_active`
- `tags` (id, name, slug, timestamps)
  - Index: `slug`
- `products` (id, category_id, brand_id, name, slug, sku, short_description, description, base_price, sale_price, is_featured, is_active, views_count, sales_count, meta_title, meta_description, timestamps, soft_deletes)
  - Index: `slug`, `sku`, `category_id`, `brand_id`, `is_active`, `is_featured`
  - Fulltext: `name`, `description`
- `product_images` (id, product_id, image_path, thumbnail_path, position, is_primary, timestamps)
  - Index: `product_id`, `is_primary`
- `product_variants` (id, product_id, sku, size, color, stock, price_adjustment, is_active, timestamps)
  - Index: `product_id`, `sku`, `stock`
- `product_tag` (product_id, tag_id) - Pivot
- `attributes` (id, name, type, timestamps) - VD: Size, Color
- `attribute_values` (id, attribute_id, value, timestamps) - VD: M, L, XL

#### **Cart & Orders**
- `carts` (id, user_id, session_id, timestamps) - Optional, nếu không dùng package
- `cart_items` (id, cart_id, product_variant_id, quantity, price, timestamps)
- `orders` (id, user_id, order_number, status, subtotal, discount, shipping_fee, tax, total, payment_method, payment_status, shipping_address_id, billing_address_id, notes, timestamps)
  - Index: `user_id`, `order_number`, `status`, `created_at`
- `order_items` (id, order_id, product_variant_id, product_name, variant_details, quantity, unit_price, subtotal, timestamps)
  - Index: `order_id`, `product_variant_id`
- `addresses` (id, user_id, name, phone, address_line1, address_line2, city, district, ward, postal_code, is_default, type, timestamps)
  - Index: `user_id`, `is_default`

#### **Coupons**
- `coupons` (id, code, type, value, min_order_amount, max_discount, usage_limit, used_count, starts_at, expires_at, is_active, timestamps)
  - Index: `code`, `is_active`, `expires_at`
- `coupon_user` (id, coupon_id, user_id, order_id, used_at) - Log sử dụng
  - Index: `coupon_id`, `user_id`

#### **Reviews**
- `reviews` (id, product_id, user_id, order_id, rating, title, content, status, is_verified_purchase, timestamps)
  - Index: `product_id`, `user_id`, `status`, `rating`
- `review_images` (id, review_id, image_path, timestamps)

#### **Wishlist**
- `wishlists` (id, user_id, product_id, timestamps)
  - Index: `user_id`, `product_id`
  - Unique: `user_id` + `product_id`

#### **Inventory (Optional - Advanced)**
- `inventory_transactions` (id, product_variant_id, type, quantity, balance_after, note, user_id, timestamps)
  - Index: `product_variant_id`, `created_at`

### 2.2 Quan hệ Eloquent

```php
// User Model
public function orders() { return $this->hasMany(Order::class); }
public function reviews() { return $this->hasMany(Review::class); }
public function wishlists() { return $this->hasMany(Wishlist::class); }
public function addresses() { return $this->hasMany(Address::class); }
public function roles() { return $this->belongsToMany(Role::class, 'model_has_roles'); }

// Category Model (Self-reference)
public function parent() { return $this->belongsTo(Category::class, 'parent_id'); }
public function children() { return $this->hasMany(Category::class, 'parent_id'); }
public function products() { return $this->hasMany(Product::class); }

// Product Model
public function category() { return $this->belongsTo(Category::class); }
public function brand() { return $this->belongsTo(Brand::class); }
public function tags() { return $this->belongsToMany(Tag::class); }
public function images() { return $this->hasMany(ProductImage::class)->orderBy('position'); }
public function variants() { return $this->hasMany(ProductVariant::class); }
public function reviews() { return $this->hasMany(Review::class); }
public function primaryImage() { return $this->hasOne(ProductImage::class)->where('is_primary', true); }

// ProductVariant Model
public function product() { return $this->belongsTo(Product::class); }
public function orderItems() { return $this->hasMany(OrderItem::class); }

// Order Model
public function user() { return $this->belongsTo(User::class); }
public function items() { return $this->hasMany(OrderItem::class); }
public function shippingAddress() { return $this->belongsTo(Address::class, 'shipping_address_id'); }
public function billingAddress() { return $this->belongsTo(Address::class, 'billing_address_id'); }
public function coupon() { return $this->belongsToMany(Coupon::class, 'coupon_user')->withPivot('used_at'); }

// OrderItem Model
public function order() { return $this->belongsTo(Order::class); }
public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }

// Review Model
public function product() { return $this->belongsTo(Product::class); }
public function user() { return $this->belongsTo(User::class); }
public function images() { return $this->hasMany(ReviewImage::class); }
```

---

## 3. ROUTES & MIDDLEWARE

### 3.1 Routes User (`routes/web.php`)

```php
// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/brands/{brand:slug}', [BrandController::class, 'show'])->name('brands.show');

// Auth (Laravel Breeze)
require __DIR__.'/auth.php';

// Protected routes (auth middleware)
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Addresses
    Route::resource('addresses', AddressController::class);
    
    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{rowId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{rowId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
    
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Reviews
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});
```

### 3.2 Routes Admin (`routes/admin.php` - Filament tự động tạo)

Filament sẽ tự động generate routes tại `/admin`:
- `/admin/login`
- `/admin/dashboard`
- `/admin/categories` (Resource)
- `/admin/brands` (Resource)
- `/admin/products` (Resource)
- `/admin/orders` (Resource)
- `/admin/users` (Resource)
- `/admin/coupons` (Resource)
- `/admin/reviews` (Resource)

---

## 4. CHI TIẾT MODULES & CHỨC NĂNG

### Module 1: Authentication & RBAC

**Models**: `User`, `Role`, `Permission` (Spatie)

**Chức năng**:
- Đăng ký, đăng nhập, đăng xuất (Breeze)
- Quên mật khẩu, reset password (email)
- Xác thực email (MustVerifyEmail)
- Cookies: HttpOnly, Secure, SameSite=lax
- Session: database driver, 2h lifetime
- Middleware: `auth`, `verified`, `role:admin`

**Seeders**:
```php
// RoleSeeder
Role::create(['name' => 'admin']);
Role::create(['name' => 'user']);

// Admin User
$admin = User::factory()->create(['email' => 'admin@fashion.test']);
$admin->assignRole('admin');
```

---

### Module 2: Catalog Management

**Models**: `Category`, `Brand`, `Tag`, `Product`, `ProductImage`, `ProductVariant`

**Admin CRUD** (Filament Resources):

1. **CategoryResource**:
   - Form: name, slug (auto), parent_id (select tree), description, image upload, position, is_active
   - Table: name, parent, products count, status, actions
   - Filters: is_active, parent_id

2. **BrandResource**:
   - Form: name, slug, logo upload, description, is_active
   - Table: logo thumbnail, name, products count, status

3. **ProductResource** (Complex CRUD):
   ```php
   // Form Schema
   - Tabs:
     * General: name, slug, sku, category, brand, tags (multi-select)
     * Description: short_description (textarea), description (RichEditor)
     * Pricing: base_price, sale_price, is_featured
     * Media: Repeater/SpatieMediaLibrary (multiple images, reorder, set primary)
     * Variants: Repeater (sku, size, color, stock, price_adjustment)
     * SEO: meta_title, meta_description
   ```

**User Frontend**:
- Listing: `/products` - Grid layout, filters sidebar, pagination
- Detail: `/products/{slug}` - Gallery, variants selector, add to cart, reviews
- Search: Fulltext + filters (category, brand, price range, size, color, sort)

**Services**:
```php
// ProductService
public function createWithMedia(array $data, array $images): Product
public function updateStock(ProductVariant $variant, int $quantity): void
public function incrementViews(Product $product): void
```

---

### Module 3: Shopping Cart

**Package**: `gloudemans/shoppingcart`

**Controller Actions**:
```php
// CartController
public function add(Request $request) {
    $variant = ProductVariant::findOrFail($request->variant_id);
    Cart::add([
        'id' => $variant->id,
        'name' => $variant->product->name,
        'qty' => $request->quantity,
        'price' => $variant->final_price,
        'options' => [
            'size' => $variant->size,
            'color' => $variant->color,
            'image' => $variant->product->primaryImage->thumbnail_path
        ]
    ]);
}

public function applyCoupon(Request $request) {
    $coupon = Coupon::where('code', $request->code)
        ->active()
        ->notExpired()
        ->first();
    
    if (!$coupon || !$coupon->canUse(Cart::subtotal())) {
        return back()->withErrors(['coupon' => 'Mã không hợp lệ']);
    }
    
    session(['coupon' => $coupon]);
    return back()->with('success', 'Áp mã thành công');
}
```

**Session Storage**:
- Cart lưu trong session với key `cart`
- Đồng bộ với DB khi user login (optional)

---

### Module 4: Checkout & Orders

**Flow**:
1. User vào `/checkout` → hiển thị summary + form địa chỉ
2. Chọn địa chỉ có sẵn hoặc nhập mới
3. Chọn phương thức thanh toán (COD / Mock Gateway)
4. Submit → `CheckoutController@process`

**CheckoutService**:
```php
public function createOrder(User $user, array $data): Order {
    DB::transaction(function () use ($user, $data) {
        // 1. Tạo order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => $this->generateOrderNumber(),
            'status' => OrderStatus::PENDING,
            'subtotal' => Cart::subtotal(),
            'discount' => $this->calculateDiscount(),
            'shipping_fee' => $this->calculateShipping(),
            'total' => $this->calculateTotal(),
            'shipping_address_id' => $data['address_id'],
            'payment_method' => $data['payment_method'],
        ]);
        
        // 2. Tạo order items
        foreach (Cart::content() as $item) {
            $variant = ProductVariant::find($item->id);
            
            // Kiểm tra tồn kho
            if ($variant->stock < $item->qty) {
                throw new InsufficientStockException();
            }
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'product_name' => $item->name,
                'variant_details' => json_encode($item->options),
                'quantity' => $item->qty,
                'unit_price' => $item->price,
                'subtotal' => $item->subtotal,
            ]);
            
            // 3. Trừ tồn kho
            $variant->decrement('stock', $item->qty);
            $variant->product->increment('sales_count', $item->qty);
        }
        
        // 4. Log coupon usage
        if ($coupon = session('coupon')) {
            $coupon->users()->attach($user->id, [
                'order_id' => $order->id,
                'used_at' => now()
            ]);
            $coupon->increment('used_count');
        }
        
        // 5. Clear cart & coupon
        Cart::destroy();
        session()->forget('coupon');
        
        // 6. Send notification/email
        event(new OrderCreated($order));
        
        return $order;
    });
}
```

**Order Status** (Enum):
- `PENDING` - Chờ xác nhận
- `CONFIRMED` - Đã xác nhận
- `PROCESSING` - Đang xử lý
- `SHIPPING` - Đang giao
- `COMPLETED` - Hoàn thành
- `CANCELLED` - Đã hủy
- `REFUNDED` - Đã hoàn tiền

**Admin Order Management** (Filament):
- Table: order_number, customer, total, status, created_at
- View: chi tiết items, address, timeline status
- Actions: change status, export CSV, print invoice
- Filters: status, date range, payment method

---

### Module 5: Reviews & Ratings

**Model**: `Review`, `ReviewImage`

**Chức năng**:
- User chỉ review được sản phẩm đã mua (verified purchase)
- Rating 1-5 sao, title, content, upload 3-5 ảnh
- Admin moderation (approve/reject)
- Hiển thị average rating trên product card

**ReviewController**:
```php
public function store(Request $request, Product $product) {
    // Validate đã mua sản phẩm
    $hasPurchased = OrderItem::whereHas('order', function($q) use ($request) {
        $q->where('user_id', $request->user()->id)
          ->where('status', OrderStatus::COMPLETED);
    })->where('product_id', $product->id)->exists();
    
    if (!$hasPurchased) {
        return back()->withErrors(['review' => 'Bạn phải mua sản phẩm mới review được']);
    }
    
    $review = $product->reviews()->create([
        'user_id' => $request->user()->id,
        'rating' => $request->rating,
        'title' => $request->title,
        'content' => $request->content,
        'status' => ReviewStatus::PENDING,
        'is_verified_purchase' => true
    ]);
    
    // Upload images
    foreach ($request->file('images', []) as $image) {
        $review->images()->create([
            'image_path' => $image->store('reviews', 'public')
        ]);
    }
}
```

**Admin**: Filament Resource với action approve/reject hàng loạt

---

### Module 6: Wishlist

**Model**: `Wishlist`

**Routes**:
- `GET /wishlist` - Danh sách
- `POST /wishlist/{product}` - Thêm
- `DELETE /wishlist/{product}` - Xóa

**Frontend**: Heart icon toggle, lưu với Alpine.js + AJAX

---

### Module 7: Admin Dashboard

**Filament Widgets**:
```php
// StatsOverviewWidget
- Doanh thu hôm nay
- Đơn hàng mới (pending)
- Sản phẩm bán chạy (top 5)
- User đăng ký mới

// OrdersChartWidget
- Line chart doanh thu 30 ngày

// LatestOrdersWidget
- Table 10 đơn gần nhất
```

**Reports**:
- Export orders CSV (Filament action)
- Stock report (low stock alert)

---

## 5. KỊCH BẢN CRUD ĐA MODEL

### Kịch bản 1: Tạo Product với Variants & Images

**Admin Flow**:
1. Vào `/admin/products/create`
2. Điền form:
   - General: "Áo Thun Nam Basic", auto slug, chọn category "T-Shirt", brand "Zara"
   - Pricing: base_price 299000, sale_price 199000
   - Media: Upload 5 ảnh → chọn ảnh 1 là primary
   - Variants: Thêm 6 variants
     * S-Đen: stock 50, price_adjustment 0
     * M-Đen: stock 50, price_adjustment 0
     * L-Đen: stock 30, price_adjustment 10000
     * S-Trắng: stock 40, price_adjustment 0
     * M-Trắng: stock 40, price_adjustment 0
     * L-Trắng: stock 20, price_adjustment 10000
3. Submit → `ProductResource::create()`

**Code**:
```php
// Filament ProductResource
public static function form(Form $form): Form {
    return $form->schema([
        Tabs::make('Product')->tabs([
            Tab::make('General')->schema([
                TextInput::make('name')->required(),
                TextInput::make('slug')->unique(ignoreRecord: true),
                Select::make('category_id')->relationship('category', 'name'),
                Select::make('brand_id')->relationship('brand', 'name'),
                Select::make('tags')->relationship('tags', 'name')->multiple(),
            ]),
            Tab::make('Media')->schema([
                SpatieMediaLibraryFileUpload::make('images')
                    ->multiple()
                    ->reorderable()
                    ->maxFiles(10)
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1'),
            ]),
            Tab::make('Variants')->schema([
                Repeater::make('variants')->schema([
                    TextInput::make('sku')->required(),
                    Select::make('size')->options(['S', 'M', 'L', 'XL']),
                    ColorPicker::make('color'),
                    TextInput::make('stock')->numeric(),
                    TextInput::make('price_adjustment')->numeric()->default(0),
                ])->relationship('variants')
            ]),
        ]),
    ]);
}
```

**Database**:
```
products: 1 record
product_images: 5 records (product_id = 1)
product_variants: 6 records (product_id = 1)
product_tag: 2 records (nếu chọn 2 tags)
```

---

### Kịch bản 2: User Checkout → Tạo Order

**User Flow**:
1. Thêm variant "M-Đen" vào cart (qty: 2)
2. Thêm variant "L-Trắng" vào cart (qty: 1)
3. Vào `/cart` → Apply coupon "SALE20" (-20%)
4. Vào `/checkout` → Chọn địa chỉ, thanh toán COD
5. Submit → `CheckoutService@createOrder`

**Database Transactions**:
```sql
-- 1. INSERT orders
INSERT INTO orders (user_id, order_number, subtotal, discount, total, status...)

-- 2. INSERT order_items (2 records)
INSERT INTO order_items (order_id, product_variant_id, quantity, unit_price...)

-- 3. UPDATE stock
UPDATE product_variants SET stock = stock - 2 WHERE id = 1; -- M-Đen
UPDATE product_variants SET stock = stock - 1 WHERE id = 6; -- L-Trắng

-- 4. UPDATE sales count
UPDATE products SET sales_count = sales_count + 3 WHERE id = 1;

-- 5. INSERT coupon_user
INSERT INTO coupon_user (coupon_id, user_id, order_id, used_at);

-- 6. UPDATE coupon used_count
UPDATE coupons SET used_count = used_count + 1 WHERE id = 1;
```

**Rollback nếu**:
- Stock không đủ → throw `InsufficientStockException`
- Coupon không hợp lệ → validation error
- Payment gateway lỗi → rollback transaction

---

## 6. KẾ HOẠCH TRIỂN KHAI (SPRINTS)

### Sprint 0: Foundation Setup (3-5 ngày)
**Mục tiêu**: Scaffold dự án, auth, RBAC, base layout

**Tasks**:
1. Cài đặt Laravel Breeze (Blade stack) - 2h
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install blade
   npm install && npm run dev
   ```
2. Cài Filament + tạo admin user - 2h
   ```bash
   composer require filament/filament
   php artisan filament:install --panels
   php artisan make:filament-user
   ```
3. Cài Spatie Permission + config - 2h
   ```bash
   composer require spatie/laravel-permission
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   php artisan migrate
   ```
4. Setup storage symlink, config upload - 1h
   ```bash
   php artisan storage:link
   ```
5. Tạo RoleSeeder (admin, user) - 1h
6. Customize layout user (Blade + TailwindCSS) - 4h
   - Header, Footer, Product Grid component
7. Config timezone `Asia/Ho_Chi_Minh`, locale `vi` - 1h

**Deliverables**:
- Auth hoạt động (register, login, logout)
- Admin panel `/admin` chỉ admin truy cập được
- Base layout responsive

---

### Sprint 1: Catalog System (5-7 ngày)
**Mục tiêu**: CRUD Category/Brand/Tag/Product/Variants/Media

**Tasks**:
1. Migration + Model: Category (self-reference) - 2h
2. Migration + Model: Brand, Tag - 1h
3. Migration + Model: Product + quan hệ - 3h
4. Migration + Model: ProductImage, ProductVariant - 2h
5. Cài Spatie Media Library - 2h
   ```bash
   composer require spatie/laravel-medialibrary
   php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
   ```
6. Filament Resource: CategoryResource - 3h
   - Form với parent_id tree select
   - Table với children count
7. Filament Resource: BrandResource - 2h
8. Filament Resource: ProductResource - 8h
   - Form tabs: General, Media, Variants, SEO
   - Media upload với Spatie
   - Variants repeater
   - Table