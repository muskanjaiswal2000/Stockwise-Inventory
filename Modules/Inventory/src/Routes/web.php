<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Categories\Index as CategoryIndex;
use Modules\Inventory\Http\Controllers\Categories\Create as CategoryCreate;
use Modules\Inventory\Http\Controllers\Categories\Edit as CategoryEdit;
use Modules\Inventory\Http\Controllers\Products\Index as ProductIndex;
use Modules\Inventory\Http\Controllers\Products\Create as ProductCreate;
use Modules\Inventory\Http\Controllers\Products\Edit as ProductEdit;
use Modules\Inventory\Http\Controllers\Products\Trashed as TrashedProducts;
use Modules\Inventory\Http\Controllers\Stock\History as StockHistory;
use Modules\Inventory\Http\Controllers\Stock\Movement as StockMovement;
use Modules\Inventory\Http\Controllers\Stock\Adjustment as StockAdjustment;
use Modules\Inventory\Http\Controllers\Suppliers\Create as SupplierCreate;
use Modules\Inventory\Http\Controllers\Suppliers\Edit as SupplierEdit;
use Modules\Inventory\Http\Controllers\Suppliers\Index as SupplierIndex;

Route::middleware(['auth', 'permission:view inventory'])
    ->prefix('inventory')
    ->name('inventory.')
    ->group(function () {

        Route::get('/', function () {
            return redirect()->route('inventory.categories.index');
        })->name('home');

        Route::middleware('permission:view categories')->group(function () {
            Route::get('/categories', CategoryIndex::class)->name('categories.index');
        });

        Route::middleware('permission:manage categories')->group(function () {
            Route::get('/categories/create', CategoryCreate::class)->name('categories.create');
            Route::get('/categories/{category}/edit', CategoryEdit::class)->name('categories.edit');
        });

        // Products
        Route::middleware('permission:view products')->group(function () {
            Route::get('/products', ProductIndex::class)->name('products.index');
        });
        Route::middleware('permission:create products')->group(function () {
            Route::get('/products/create', ProductCreate::class)->name('products.create');
        });
        Route::middleware('permission:restore products')->group(function () {
            Route::get('/products/trashed', TrashedProducts::class)->name('products.trashed');
        });
        Route::middleware('permission:edit products')->group(function () {
            Route::get('/products/{product}/edit', ProductEdit::class)->name('products.edit');
        });

        // Suppliers
        Route::middleware('permission:view suppliers')->group(function () {
            Route::get('/suppliers', SupplierIndex::class)->name('suppliers.index');
        });
        Route::middleware('permission:manage suppliers')->group(function () {
            Route::get('/suppliers/create', SupplierCreate::class)->name('suppliers.create');
            Route::get('/suppliers/{supplier}/edit', SupplierEdit::class)->name('suppliers.edit');
        });

        // Stock Movement
        Route::middleware('permission:manage stock')->group(function () {
            Route::get('/stock/movement', StockMovement::class)->name('stock.movement');
            Route::get('/stock/adjustment', StockAdjustment::class)->name('stock.adjustment');
        });
        Route::middleware('permission:view stock')->group(function () {
            Route::get('/stock/history', StockHistory::class)->name('stock.history');
            Route::get('/stock/history/export/excel', \Modules\Inventory\Http\Controllers\Stock\ExportExcel::class)
                ->name('stock.history.export.excel');
            Route::get('/stock/history/export/pdf', \Modules\Inventory\Http\Controllers\Stock\ExportPdf::class)
                ->name('stock.history.export.pdf');
        });
    });

Route::get('/catalog/{token}', \Modules\Inventory\Http\Controllers\PublicCatalog::class)
    ->name('inventory.catalog');
Route::post('/catalog/{token}/add/{product}', \Modules\Inventory\Http\Controllers\PublicCatalogAdd::class)
    ->whereNumber('product')
    ->name('inventory.catalog.add');
Route::get('/catalog/{token}/qr', \Modules\Inventory\Http\Controllers\PublicCatalogQr::class)
    ->name('inventory.catalog.qr');
Route::get('/catalog/{token}/checkout', \Modules\Inventory\Http\Controllers\PublicCheckout::class)
    ->name('inventory.catalog.checkout');
Route::post('/catalog/{token}/checkout', \Modules\Inventory\Http\Controllers\PublicCheckoutSubmit::class)
    ->name('inventory.catalog.checkout.submit');
Route::match(['get', 'post'], '/catalog/{token}/return', \Modules\Inventory\Http\Controllers\PublicReturn::class)
    ->name('inventory.catalog.return');
Route::post('/catalog/{token}/return/submit', \Modules\Inventory\Http\Controllers\PublicReturn::class)
    ->name('inventory.catalog.return.submit');
Route::get('/catalog/{token}/requests', \Modules\Inventory\Http\Controllers\PublicRequestHistory::class)
    ->name('inventory.catalog.requests');

Route::middleware(['auth', 'role:admin', 'permission:manage staff permissions'])
    ->prefix('inventory')
    ->name('inventory.')
    ->group(function () {
        Route::get('/staff/create', \Modules\Inventory\Http\Controllers\Staff\Create::class)
            ->name('staff.create');
        Route::get('/staff/{staff}/edit', \Modules\Inventory\Http\Controllers\Staff\Edit::class)
            ->name('staff.edit');
        Route::get('/staff/{staff}/details', \Modules\Inventory\Http\Controllers\Staff\DetailsEdit::class)
            ->name('staff.details.edit');
        Route::get('/staff/permissions', \Modules\Inventory\Http\Controllers\Staff\PermissionsPage::class)
            ->name('staff.permissions');
        Route::post('/staff/permissions', \Modules\Inventory\Http\Controllers\Staff\UpdatePermissions::class)
            ->name('staff.permissions.update');
    });



    
