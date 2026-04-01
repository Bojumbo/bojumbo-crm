<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CounterpartyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\DocumentController;
// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class , 'login']);
});
// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
            return view('welcome');
        }
        );

        Route::prefix('counterparties')->name('counterparties.')->group(function () {
            Route::get('/', [CounterpartyController::class , 'index'])->name('index');
            Route::post('/', [CounterpartyController::class , 'store'])->name('store');
            Route::patch('/{counterparty}', [CounterpartyController::class , 'update'])->name('update');
            Route::post('/quick', [CounterpartyController::class, 'quickStore'])->name('quick');
            Route::delete('/{counterparty}', [CounterpartyController::class , 'destroy'])->name('destroy');
        }
        );
        Route::prefix('deals')->name('deals.')->group(function () {
            Route::get('/', [App\Http\Controllers\DealController::class , 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\DealController::class , 'store'])->name('store');
            Route::patch('/{deal}', [App\Http\Controllers\DealController::class , 'update'])->name('update');
            Route::patch('/{deal}/move', [App\Http\Controllers\DealController::class , 'move'])->name('move');
            Route::delete('/{deal}', [App\Http\Controllers\DealController::class , 'destroy'])->name('destroy');
        }
        );
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [App\Http\Controllers\ProductController::class , 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\ProductController::class , 'store'])->name('store');
            Route::patch('/{product}', [App\Http\Controllers\ProductController::class , 'update'])->name('update');
            Route::delete('/{product}', [App\Http\Controllers\ProductController::class , 'destroy'])->name('destroy');
        }
        );
        // Admin Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/pipelines', [App\Http\Controllers\PipelineController::class , 'index'])->name('pipelines.index');
            Route::post('/pipelines', [App\Http\Controllers\PipelineController::class , 'store'])->name('pipelines.store');
            Route::put('/pipelines/{pipeline}', [App\Http\Controllers\PipelineController::class , 'update'])->name('pipelines.update');
            Route::delete('/pipelines/{pipeline}', [App\Http\Controllers\PipelineController::class , 'destroy'])->name('pipelines.destroy');

            Route::get('/settings', [App\Http\Controllers\SettingsController::class , 'index'])->name('settings.index');
            Route::post('/settings', [App\Http\Controllers\SettingsController::class , 'update'])->name('settings.update');

            Route::get('/fields', [App\Http\Controllers\Admin\FieldController::class , 'index'])->name('fields.index');
            Route::post('/fields', [App\Http\Controllers\Admin\FieldController::class , 'store'])->name('fields.store');
            Route::delete('/fields/{field}', [App\Http\Controllers\Admin\FieldController::class , 'destroy'])->name('fields.destroy');

            // Document Settings
            Route::get('/templates', [App\Http\Controllers\DocumentController::class , 'index'])->name('settings.templates');
            Route::post('/templates', [App\Http\Controllers\DocumentController::class , 'store'])->name('settings.templates.store');
            Route::put('/templates/{template}', [App\Http\Controllers\DocumentController::class , 'update'])->name('settings.templates.update');
            Route::delete('/templates/{template}', [App\Http\Controllers\DocumentController::class , 'destroy'])->name('settings.templates.destroy');

            Route::get('/document-tables', [App\Http\Controllers\DocumentTableController::class , 'index'])->name('settings.document_tables.index');
            Route::post('/document-tables', [App\Http\Controllers\DocumentTableController::class , 'store'])->name('settings.document_tables.store');
            Route::put('/document-tables/{table}', [App\Http\Controllers\DocumentTableController::class , 'update'])->name('settings.document_tables.update');
            Route::delete('/document-tables/{table}', [App\Http\Controllers\DocumentTableController::class , 'destroy'])->name('settings.document_tables.destroy');
        }
        );

        Route::prefix('automations')->name('automations.')->group(function () {
            Route::post('/', [App\Http\Controllers\AutomationController::class , 'store'])->name('store');
            Route::put('/{automation}', [App\Http\Controllers\AutomationController::class , 'update'])->name('update');
            Route::delete('/{automation}', [App\Http\Controllers\AutomationController::class , 'destroy'])->name('destroy');
        });

        Route::get('/statistics', [App\Http\Controllers\StatisticsController::class , 'index'])->name('statistics.index');
        Route::get('/activity', [App\Http\Controllers\ActivityController::class , 'index'])->name('activity.index');
        Route::post('/activity', [App\Http\Controllers\ActivityController::class , 'store'])->name('activity.store');
        Route::post('/products/quick', [ProductController::class, 'quickStore'])->name('products.quick');
        Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

        // Google API & Docs
        Route::get('/google/connect', [GoogleOAuthController::class , 'redirectToGoogle'])->name('google.connect');
        Route::get('/google/callback', [GoogleOAuthController::class , 'handleCallback'])->name('google.callback');
        Route::post('/deals/{deal}/generate-doc', [DocumentController::class , 'generate'])->name('deals.generate-doc');
        Route::get('/deals/{deal}/files', [DocumentController::class , 'listFiles'])->name('deals.files');
        Route::post('/deals/{deal}/upload', [DocumentController::class , 'upload'])->name('deals.upload');
        Route::get('/api/templates', [DocumentController::class , 'templates'])->name('documents.templates');
    });