<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\AdminContactController;
use App\Http\Controllers\Api\AdminCustomerController;
use App\Http\Controllers\Api\AdminReviewController;
use App\Http\Controllers\Api\Admin\CompanyInfoController;
use App\Http\Controllers\Api\Admin\CompanyStatsController;
use App\Http\Controllers\Api\Admin\ContactInfoController;
use App\Http\Controllers\Api\Admin\DepartmentController;
use App\Http\Controllers\Api\Admin\SocialLinkController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CostCalculatorController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'OK',
            'timestamp' => now()->toISOString(),
            'service' => 'BuildTools BS API',
            'version' => '1.0.0'
        ]);
    })->name('health');

    // Test endpoint for CORS and API availability
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is working!',
            'cors' => 'CORS headers should be present'
        ]);
    })->name('test');

    // Auth routes
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

    // Public product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    // Public category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/statistics', [CategoryController::class, 'statistics'])->name('categories.statistics');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');

    // Public supplier routes
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/{id}', [SupplierController::class, 'show'])->name('suppliers.show');

    // Public brand routes
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/{id}', [BrandController::class, 'show'])->name('brands.show');
    Route::get('/brands/{id}/products', [BrandController::class, 'products'])->name('brands.products');

    // Public reviews routes
    Route::get('/products/{id}/reviews', [ReviewController::class, 'productReviews'])->name('products.reviews');
    Route::post('/reviews/{id}/helpful', [ReviewController::class, 'markHelpful'])->name('reviews.helpful');

    // Cost calculator (public)
    Route::post('/calculator', [CostCalculatorController::class, 'calculate'])->name('calculator');

    // Contact routes (public)
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/contact/departments', [ContactController::class, 'departments'])->name('contact.departments');
    Route::get('/contact/info', [ContactController::class, 'info'])->name('contact.info');

    // Newsletter routes (public)
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
    Route::get('/newsletter/status', [NewsletterController::class, 'status'])->name('newsletter.status');
    Route::post('/newsletter/preferences', [NewsletterController::class, 'updatePreferences'])->name('newsletter.preferences');
    Route::get('/newsletter/preferences/available', [NewsletterController::class, 'availablePreferences'])->name('newsletter.preferences.available');

    // ==========================================
    // 🌐 PUBLIC CONTENT MANAGEMENT APIs
    // ==========================================
    // These APIs provide public access to company content for website display
    Route::prefix('public')->group(function () {
        Route::get('/company-info', [App\Http\Controllers\Api\Public\CompanyInfoController::class, 'index'])->name('public.company-info');
        Route::get('/company-stats', [App\Http\Controllers\Api\Public\CompanyStatsController::class, 'index'])->name('public.company-stats');
        Route::get('/contact-info', [App\Http\Controllers\Api\Public\ContactInfoController::class, 'index'])->name('public.contact-info');
        Route::get('/social-links', [App\Http\Controllers\Api\Public\SocialLinksController::class, 'index'])->name('public.social-links');
        Route::get('/page-content', [App\Http\Controllers\Api\Public\PageContentController::class, 'index'])->name('public.page-content');
        Route::get('/company-values', [App\Http\Controllers\Api\Public\CompanyValueController::class, 'index'])->name('public.company-values');
        Route::get('/company-milestones', [App\Http\Controllers\Api\Public\CompanyMilestoneController::class, 'index'])->name('public.company-milestones');
        Route::get('/company-story', [App\Http\Controllers\Api\Public\CompanyStoryController::class, 'index'])->name('public.company-story');
        Route::get('/team-members', [App\Http\Controllers\Api\Public\TeamMemberController::class, 'index'])->name('public.team-members');
        Route::get('/departments', [App\Http\Controllers\Api\Public\DepartmentController::class, 'index'])->name('public.departments');
        Route::get('/faqs', [App\Http\Controllers\Api\Public\FAQController::class, 'index'])->name('public.faqs');
        Route::get('/certifications', [App\Http\Controllers\Api\Public\CertificationController::class, 'index'])->name('public.certifications');
    });
});

// Protected routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile'); // Frontend expects this exact path
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    
    // Auth test endpoint
    Route::get('/auth-test', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Authentication is working!',
            'user_id' => $request->user()->id,
            'user_name' => $request->user()->name,
            'timestamp' => now()->toISOString()
        ]);
    })->name('auth.test');
    
    // Email verification routes
    Route::post('/email/send-verification', [AuthController::class, 'sendVerification'])->name('verification.send');
    Route::post('/email/verify', [AuthController::class, 'verifyEmail'])->name('verification.verify');

    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
        Route::post('/documents', [UserController::class, 'uploadDocuments'])->name('user.documents');
        
        // User orders
        Route::get('/orders', [OrderController::class, 'index'])->name('user.orders');
    });

    // Product routes (admin only)
    Route::middleware('role:admin')->prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Category routes (admin only)
    Route::middleware('role:admin')->prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Admin Dashboard routes (admin only)
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('admin.dashboard.stats');
            Route::get('/recent-activity', [AdminDashboardController::class, 'recentActivity'])->name('admin.dashboard.recent-activity');
            Route::get('/overview', [AdminDashboardController::class, 'overview'])->name('admin.dashboard.overview');
        });

        // Admin Products Management routes
        Route::prefix('products')->group(function () {
            Route::get('/', [AdminProductController::class, 'index'])->name('admin.products.index');
            Route::get('/stats', [AdminProductController::class, 'stats'])->name('admin.products.stats');
            Route::post('/', [AdminProductController::class, 'store'])->name('admin.products.store');
            Route::get('/{id}', [AdminProductController::class, 'show'])->name('admin.products.show');
            Route::put('/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
            Route::post('/{id}', [AdminProductController::class, 'update'])->name('admin.products.update.formdata'); // For FormData updates
            Route::patch('/{id}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');
            Route::patch('/{id}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('admin.products.toggle-featured');
            Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
        });

        // Admin Categories Management routes
        Route::prefix('categories')->group(function () {
            Route::get('/', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
            Route::get('/stats', [AdminCategoryController::class, 'stats'])->name('admin.categories.stats');
            Route::post('/', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
            Route::get('/{id}', [AdminCategoryController::class, 'show'])->name('admin.categories.show');
            Route::put('/{id}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
            Route::patch('/{id}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
            Route::delete('/{id}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
        });

        // Admin Customers Management routes
        Route::prefix('customers')->group(function () {
            Route::get('/stats', [AdminCustomerController::class, 'stats'])->name('admin.customers.stats');
            Route::get('/activity-stats', [AdminCustomerController::class, 'activityStats'])->name('admin.customers.activity-stats');
            Route::get('/export', [AdminCustomerController::class, 'export'])->name('admin.customers.export');
            Route::post('/advanced-search', [AdminCustomerController::class, 'advancedSearch'])->name('admin.customers.advanced-search');
            Route::post('/send-notification', [AdminCustomerController::class, 'sendNotification'])->name('admin.customers.send-notification');
            Route::get('/', [AdminCustomerController::class, 'index'])->name('admin.customers.index');
            Route::get('/{id}', [AdminCustomerController::class, 'show'])->name('admin.customers.show');
            Route::patch('/{id}/status', [AdminCustomerController::class, 'updateStatus'])->name('admin.customers.update-status');
        });
    });

    // Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/{id}', [OrderController::class, 'update'])->name('orders.update');
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('/{id}/invoice', [OrderController::class, 'generateInvoice'])->name('orders.invoice');
        Route::delete('/{id}', [OrderController::class, 'cancel'])->name('orders.cancel');
    });

    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::post('/', [PaymentController::class, 'process'])->name('payments.process');
        Route::post('/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    });

    // Shipment routes
    Route::prefix('shipments')->group(function () {
        Route::get('/{id}', [ShipmentController::class, 'show'])->name('shipments.show');
        Route::patch('/{id}/track', [ShipmentController::class, 'updateTracking'])->name('shipments.track');
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    // Cart routes
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add', [CartController::class, 'add'])->name('cart.add');
        Route::put('/update', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
        Route::delete('/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
    });

    // Wishlist routes
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/add', [WishlistController::class, 'add'])->name('wishlist.add');
        Route::delete('/remove/{productId}', [WishlistController::class, 'remove'])->name('wishlist.remove');
        Route::post('/move-to-cart/{productId}', [WishlistController::class, 'moveToCart'])->name('wishlist.move');
        Route::delete('/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
        Route::get('/check/{productId}', [WishlistController::class, 'check'])->name('wishlist.check');
        Route::post('/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    });

    // Address routes
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('addresses.index');
        Route::post('/', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('/{id}', [AddressController::class, 'show'])->name('addresses.show');
        Route::put('/{id}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/{id}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('/{id}/default', [AddressController::class, 'makeDefault'])->name('addresses.default');
    });

    // Reviews routes
    Route::prefix('reviews')->group(function () {
        Route::post('/', [ReviewController::class, 'store'])->name('reviews.store');
        Route::put('/{id}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Enhanced Payment routes
    Route::prefix('payments')->group(function () {
        Route::post('/process', [PaymentController::class, 'process'])->name('payments.process');
        Route::get('/{id}/status', [PaymentController::class, 'status'])->name('payments.status');
        Route::post('/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    });

    // Enhanced Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/{id}/tracking', [ShipmentController::class, 'tracking'])->name('orders.tracking');
    });

    // Supplier routes (for suppliers)
    Route::middleware('role:supplier')->prefix('supplier')->group(function () {
        Route::get('/profile', [SupplierController::class, 'profile'])->name('supplier.profile');
        Route::put('/profile', [SupplierController::class, 'updateProfile'])->name('supplier.profile.update');
        Route::post('/certifications', [SupplierController::class, 'uploadCertifications'])->name('supplier.certifications');
    });

    // Admin Review Management routes
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin/reviews')->group(function () {
        Route::get('/stats', [AdminReviewController::class, 'stats'])->name('admin.reviews.stats');
        Route::get('/analytics', [AdminReviewController::class, 'analytics'])->name('admin.reviews.analytics');
        Route::get('/', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('/{id}', [AdminReviewController::class, 'show'])->name('admin.reviews.show');
        Route::put('/{id}/status', [AdminReviewController::class, 'updateStatus'])->name('admin.reviews.updateStatus');
        Route::post('/bulk', [AdminReviewController::class, 'bulk'])->name('admin.reviews.bulk');
    });

    // Admin Contact Messages Management routes
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin/contact-messages')->group(function () {
        Route::get('/stats', [AdminContactController::class, 'stats'])->name('admin.contact.stats');
        Route::get('/analytics', [AdminContactController::class, 'analytics'])->name('admin.contact.analytics');
        Route::get('/', [AdminContactController::class, 'index'])->name('admin.contact.index');
        Route::get('/{id}', [AdminContactController::class, 'show'])->name('admin.contact.show');
        Route::put('/{id}', [AdminContactController::class, 'update'])->name('admin.contact.update');
        Route::delete('/{id}', [AdminContactController::class, 'destroy'])->name('admin.contact.destroy');
        Route::post('/bulk', [AdminContactController::class, 'bulkAction'])->name('admin.contact.bulk');
    });

    // Content Management System Routes
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        // Company Info Management
        Route::get('/company-info', [App\Http\Controllers\Api\Admin\CompanyInfoController::class, 'index'])->name('admin.company-info.index');
        Route::put('/company-info', [App\Http\Controllers\Api\Admin\CompanyInfoController::class, 'update'])->name('admin.company-info.update');

        // Company Stats Management
        Route::get('/company-stats', [App\Http\Controllers\Api\Admin\CompanyStatsController::class, 'index'])->name('admin.company-stats.index');
        Route::put('/company-stats', [App\Http\Controllers\Api\Admin\CompanyStatsController::class, 'update'])->name('admin.company-stats.update');

        // Contact Info Management
        Route::get('/contact-info', [App\Http\Controllers\Api\Admin\ContactInfoController::class, 'index'])->name('admin.contact-info.index');
        Route::put('/contact-info', [App\Http\Controllers\Api\Admin\ContactInfoController::class, 'update'])->name('admin.contact-info.update');

        // Departments Management
        Route::apiResource('departments', App\Http\Controllers\Api\Admin\DepartmentController::class);
        Route::put('/departments/order', [App\Http\Controllers\Api\Admin\DepartmentController::class, 'updateOrder'])->name('admin.departments.order');

        // Social Links Management
        Route::apiResource('social-links', App\Http\Controllers\Api\Admin\SocialLinkController::class);
        Route::put('/social-links/order', [App\Http\Controllers\Api\Admin\SocialLinkController::class, 'updateOrder'])->name('admin.social-links.order');

        // Team Members Management
        Route::apiResource('team-members', App\Http\Controllers\Api\Admin\TeamMemberController::class);

        // Company Values Management
        Route::apiResource('company-values', App\Http\Controllers\Api\Admin\CompanyValueController::class);

        // Company Milestones Management
        Route::apiResource('company-milestones', App\Http\Controllers\Api\Admin\CompanyMilestoneController::class);

        // Company Story Management
        Route::get('/company-story', [App\Http\Controllers\Api\Admin\CompanyStoryController::class, 'index'])->name('admin.company-story.index');
        Route::put('/company-story', [App\Http\Controllers\Api\Admin\CompanyStoryController::class, 'update'])->name('admin.company-story.update');

        // Page Content Management
        Route::get('/page-content', [App\Http\Controllers\Api\Admin\PageContentController::class, 'index'])->name('admin.page-content.index');
        Route::put('/page-content', [App\Http\Controllers\Api\Admin\PageContentController::class, 'update'])->name('admin.page-content.update');

        // FAQs Management
        Route::apiResource('faqs', App\Http\Controllers\Api\Admin\FAQController::class);

        // Certifications Management
        Route::apiResource('certifications', App\Http\Controllers\Api\Admin\CertificationController::class);
    });
});
