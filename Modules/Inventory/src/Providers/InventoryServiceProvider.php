<?php

namespace Modules\Inventory\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Modules\Inventory\Http\Controllers\Categories\Create;
use Modules\Inventory\Http\Controllers\Categories\Edit;
use Modules\Inventory\Http\Controllers\Categories\Index;
use Modules\Inventory\Http\Controllers\Products\Create as ProductCreate;
use Modules\Inventory\Http\Controllers\Products\Edit as ProductEdit;
use Modules\Inventory\Http\Controllers\Products\Index as ProductIndex;
use Modules\Inventory\Http\Controllers\Products\Trashed as TrashedProducts;
use Modules\Inventory\Http\Controllers\Dashboard as InventoryDashboard;
use Modules\Inventory\Http\Controllers\Stock\Adjustment as StockAdjustment;
use Modules\Inventory\Http\Controllers\Stock\History as StockHistory;
use Modules\Inventory\Http\Controllers\Stock\Movement as StockMovement;
use Modules\Inventory\Http\Controllers\Staff\Create as StaffCreate;
use Modules\Inventory\Http\Controllers\Suppliers\Create as SupplierCreate;
use Modules\Inventory\Http\Controllers\Suppliers\Edit as SupplierEdit;
use Modules\Inventory\Http\Controllers\Suppliers\Index as SupplierIndex;

class InventoryServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Inventory';
    protected string $moduleNameLower = 'inventory';

    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge all module config files
        $this->registerModuleConfigs();

        // Register EventServiceProvider if exists
        $eventProvider = "Modules\\{$this->moduleName}\\Providers\\{$this->moduleName}EventServiceProvider";
        if (class_exists($eventProvider)) {
            $this->app->register($eventProvider);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishModuleConfigs();
        $this->registerCommands();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerMigrations();
        $this->registerMiddleware();
        $this->registerComponents();
    }

    /**
     * Dynamically merge all config files from the module's Config directory.
     */
    protected function registerModuleConfigs(): void
    {
        $configPath = __DIR__ . '/../Config';
        foreach (glob($configPath . '/*.php') as $file) {
            $filename = basename($file, '.php');

            if ($filename === 'config') {
                // Merge main config file as 'client'
                $this->mergeConfigFrom($file, $this->moduleNameLower);
            } else {
                // Merge others as 'client.filename'
                $this->mergeConfigFrom($file, "$this->moduleNameLower::$filename");
            }
        }
    }

    /**
     * Publish all module config files.
     */
    protected function publishModuleConfigs(): void
    {
        $configPath = __DIR__ . '/../Config';
        if (File::isDirectory($configPath)) {
            $publish = [];
            foreach (File::files($configPath) as $file) {
                if ($file->getExtension() === 'php') {
                    $publish[$file->getRealPath()] = config_path($file->getFilename());
                }
            }
            if (!empty($publish)) {
                $this->publishes($publish, ['config', "{$this->moduleNameLower}-config"]);
            }
        }
    }

    /**
     * Register commands automatically from Console folder.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $commandPath = __DIR__ . '/../Console';
            if (File::isDirectory($commandPath)) {
                foreach (File::files($commandPath) as $file) {
                    $class = "Modules\\{$this->moduleName}\\Console\\" . $file->getBasename('.php');
                    if (class_exists($class)) {
                        $this->commands([$class]);
                    }
                }
            }
        }
    }

    /**
     * Register routes.
     */
    /**
     * Register routes.
     */
    protected function registerRoutes(): void
    {
        try {
            $webRoutePath = __DIR__ . '/../Routes/web.php';
            $apiRoutePath = __DIR__ . '/../Routes/api.php';

            if (file_exists($webRoutePath)) {
                \Illuminate\Support\Facades\Route::middleware('web')
                    ->group($webRoutePath);
            }

            if (file_exists($apiRoutePath)) {
                \Illuminate\Support\Facades\Route::prefix('api')
                    ->middleware('api')
                    ->group($apiRoutePath);
            }
        } catch (\Exception $e) {
            Log::error("Error in {$this->moduleName}ServiceProvider registerRoutes: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Register views.
     */
    protected function registerViews(): void
    {
        $viewPath = resource_path("views/modules/{$this->moduleNameLower}");
        $sourcePath = __DIR__ . '/../Resources/views';

        if (File::isDirectory($sourcePath)) {
            $this->publishes([
                $sourcePath => $viewPath
            ], ['views', "{$this->moduleNameLower}-views"]);

            $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'inventory');
        }
    }

    /**
     * Register translations.
     */
    protected function registerTranslations(): void
    {
        $langPath = __DIR__ . '/../Resources/lang';
        if (File::isDirectory($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        }
    }

    /**
     * Register migrations.
     */
    protected function registerMigrations(): void
    {
        $migrationPath = __DIR__ . '/../Database/Migrations';
        if (File::isDirectory($migrationPath)) {
            $this->loadMigrationsFrom($migrationPath);
        }
    }

    /**
     * Register Middleware.
     */

    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];


        //   $router->aliasMiddleware('{name}', Middleware::class);

    }

    /**
     * Register Blade/Livewire components.
     */
    protected function registerComponents(): void
    {
        Livewire::component('modules.inventory.livewire.categories.index', Index::class);
        Livewire::component('modules.inventory.livewire.categories.create', Create::class);
        Livewire::component('modules.inventory.livewire.categories.edit', Edit::class);
        Livewire::component('modules.inventory.livewire.products.index', ProductIndex::class);
        Livewire::component('modules.inventory.livewire.products.create', ProductCreate::class);
        Livewire::component('modules.inventory.livewire.products.edit', ProductEdit::class);
        Livewire::component('modules.inventory.livewire.products.trashed', TrashedProducts::class);
        Livewire::component('modules.inventory.livewire.suppliers.index', SupplierIndex::class);
        Livewire::component('modules.inventory.livewire.suppliers.create', SupplierCreate::class);
        Livewire::component('modules.inventory.livewire.suppliers.edit', SupplierEdit::class);
        Livewire::component('modules.inventory.livewire.stock.movement', StockMovement::class);
        Livewire::component('modules.inventory.livewire.stock.history', StockHistory::class);
        Livewire::component('modules.inventory.livewire.stock.adjustment', StockAdjustment::class);
        Livewire::component('modules.inventory.livewire.dashboard', InventoryDashboard::class);
        Livewire::component('modules.inventory.livewire.staff.create', StaffCreate::class);
    }
}
