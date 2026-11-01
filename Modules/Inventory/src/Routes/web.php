<?php

use Illuminate\Support\Facades\Route;

// Category controllers
use Modules\Inventory\Http\Controllers\Categories\Index as CategoryIndex;
use Modules\Inventory\Http\Controllers\Categories\Create as CategoryCreate;
use Modules\Inventory\Http\Controllers\Categories\Edit as CategoryEdit;

// Product controllers
use Modules\Inventory\Http\Controllers\Products\Index as ProductIndex;
use Modules\Inventory\Http\Controllers\Products\Create as ProductCreate;
use Modules\Inventory\Http\Controllers\Products\Edit as ProductEdit;
use Modules\Inventory\Http\Controllers\Products\Trashed as ProductTrashed;

// Stock controllers
use Modules\Inventory\Http\Controllers\Stock\History as StockHistory;
use Modules\Inventory\Http\Controllers\Stock\Movement as StockMovement;
use Modules\Inventory\Http\Controllers\Stock\Adjustment as StockAdjustment;
use Modules\Inventory\Http\Controllers\Stock\ExportExcel;
use Modules\Inventory\Http\Controllers\Stock\ExportPdf;

// Supplier controllers
use Modules\Inventory\Http\Controllers\Suppliers\Index as SupplierIndex;
use Modules\Inventory\Http\Controllers\Suppliers\Create as SupplierCreate;
use Modules\Inventory\Http\Controllers\Suppliers\Edit as SupplierEdit;

// Staff controllers
use Modules\Inventory\Http\Controllers\Staff\Create as StaffCreate;
use Modules\Inventory\Http\Controllers\Staff\PermissionsPage;
use Modules\Inventory\Http\Controllers\Staff\UpdatePermissions;
use Modules\Inventory\Http\Controllers\Staff\DetailsEdit as StaffDetailsEdit;
use Modules\Inventory\Http\Controllers\Staff\UpdateDetails;
use Modules\Inventory\Http\Controllers\Staff\ResetPassword;
use Modules\Inventory\Http\Controllers\Staff\DeleteStaff;
use Modules\Inventory\Http\Controllers\Staff\Edit as StaffEdit;

// Public Catalog and QR Code controllers
use Modules\Inventory\Http\Controllers\PublicCatalog;
use Modules\Inventory\Http\Controllers\PublicCatalogCart;
use Modules\Inventory\Http\Controllers\PublicCatalogAdd;
use Modules\Inventory\Http\Controllers\PublicCatalogQr;
use Modules\Inventory\Http\Controllers\PublicCheckout;
use Modules\Inventory\Http\Controllers\PublicCheckoutSubmit;
use Modules\Inventory\Http\Controllers\PublicRequestHistory;
use Modules\Inventory\Http\Controllers\PublicReturn;


/*
|--------------------------------------------------------------------------
| Public Product Catalog and QR Code Routes
|--------------------------------------------------------------------------
| Customers can access these routes by scanning a QR code.
| These routes do not require authentication.
*/

Route::prefix('inventory/catalog/{token}')->group(function () {

    Route::get('/', PublicCatalog::class)
        ->name('inventory.catalog');

    Route::get('/qr', PublicCatalogQr::class)
        ->name('inventory.catalog.qr');

    Route::post('/add/{product}', PublicCatalogAdd::class)
        ->name('inventory.catalog.add');

    Route::post('/decrement/{product}', [PublicCatalogCart::class, 'decrement'])
        ->name('inventory.catalog.decrement');

    Route::post('/clear', [PublicCatalogCart::class, 'clear'])
        ->name('inventory.catalog.clear');

    Route::get('/checkout', PublicCheckout::class)
        ->name('inventory.catalog.checkout');

    Route::post('/checkout', PublicCheckoutSubmit::class)
        ->name('inventory.catalog.checkout.submit');

    Route::get('/requests', PublicRequestHistory::class)
        ->name('inventory.catalog.requests');

    Route::get('/return', PublicReturn::class)
        ->name('inventory.catalog.return');

    Route::post('/return', PublicReturn::class)
        ->name('inventory.catalog.return.submit');
});


/*
|--------------------------------------------------------------------------
| Authenticated Inventory Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:view inventory'])
    ->prefix('inventory')
    ->name('inventory.')
    ->group(function () {

        // Categories

        Route::middleware('permission:view categories')->group(function () {
            Route::get('/categories', CategoryIndex::class)
                ->name('categories.index');
        });

        Route::middleware('permission:manage categories')->group(function () {
            Route::get('/categories/create', CategoryCreate::class)
                ->name('categories.create');

            Route::get('/categories/{category}/edit', CategoryEdit::class)
                ->name('categories.edit');
        });


        // Products

        Route::middleware('permission:view products')->group(function () {
            Route::get('/products', ProductIndex::class)
                ->name('products.index');

            Route::get('/products/trashed', ProductTrashed::class)
                ->name('products.trashed');
        });

        Route::middleware('permission:create products')->group(function () {
            Route::get('/products/create', ProductCreate::class)
                ->name('products.create');
        });

        Route::middleware('permission:edit products')->group(function () {
            Route::get('/products/{product}/edit', ProductEdit::class)
                ->name('products.edit');
        });


        // Suppliers

        Route::middleware('permission:view suppliers')->group(function () {
            Route::get('/suppliers', SupplierIndex::class)
                ->name('suppliers.index');
        });

        Route::middleware('permission:manage suppliers')->group(function () {
            Route::get('/suppliers/create', SupplierCreate::class)
                ->name('suppliers.create');

            Route::get('/suppliers/{supplier}/edit', SupplierEdit::class)
                ->name('suppliers.edit');
        });

        // Stock

        Route::middleware('permission:view stock')->group(function () {
            Route::get('/stock/history', StockHistory::class)
                ->name('stock.history');

            Route::get('/stock/export/excel', ExportExcel::class)
                ->name('stock.history.export.excel');

            Route::get('/stock/export/pdf', ExportPdf::class)
                ->name('stock.history.export.pdf');
        });

        Route::middleware('permission:manage stock')->group(function () {
            Route::get('/stock/movement', StockMovement::class)
                ->name('stock.movement');

            Route::get('/stock/adjustment', StockAdjustment::class)
                ->name('stock.adjustment');
        });


        /*
        |--------------------------------------------------------------------------
        | Staff Management and Permission Assignment
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:super_admin|admin|manager')->group(function () {

            Route::get('/staff/permissions', PermissionsPage::class)
                ->name('staff.permissions');

            Route::post('/staff/permissions', UpdatePermissions::class)
                ->name('staff.update');

            Route::get('/staff/{staff}/edit', StaffEdit::class)
                ->name('staff.edit');
        });


        /*
        |--------------------------------------------------------------------------
        | Create and Edit Account Details
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:super_admin|admin')->group(function () {

            Route::get('/staff/create', StaffCreate::class)
                ->name('staff.create');

            Route::get('/staff/{staff}/details', StaffDetailsEdit::class)
                ->name('staff.details.edit');

            Route::put('/staff/{staff}/details', UpdateDetails::class)
                ->name('staff.details.update');
        });


        /*
        |--------------------------------------------------------------------------
        | Reset Staff Password
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:super_admin')->group(function () {

            Route::post('/staff/{staff}/reset-password', ResetPassword::class)
                ->name('staff.reset-password');
        });


        /*
        |--------------------------------------------------------------------------
        | Delete Staff Accounts
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:super_admin|admin')->group(function () {

            Route::delete('/staff/{staff}', DeleteStaff::class)
                ->name('staff.destroy');
        });
    });
