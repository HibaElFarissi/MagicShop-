<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogDetailsController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InfosController;
use App\Http\Controllers\MoreCategoryController;
use App\Http\Controllers\QuotesController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\slideController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[StoreController::class,'index']);

Route::resource('products',ProductController::class);
Route::resource('categories',CategoryController::class);
Route::resource('abouts',AboutController::class);
Route::get('about',[AboutController::class, 'about'])->name('about');
Route::resource('quotes',QuotesController::class);
Route::resource('feedback',FeedbackController::class);


Route::get('/MoreCategory',[MoreCategoryController::class,'index'])->name('MoreCategory');
Route::get('/cart',[CartController::class,'index'])->name('cart');
Route::get('/checkout',[CheckoutController::class,'index'])->name('checkout');


// page de contact:
Route::get('/inbox',[ContactController::class,'index'])->name('inbox');



// Pages:
// Route::get('/contact',[ContactController::class,'index']);
Route::get('/blog',[BlogController::class,'index'])->name('blog');
Route::get('/blog-details',[BlogDetailsController::class,'index'])->name('blog-details');
Route::get('/shop',[ShopController::class,'index'])->name('shop');
//Email - contact :
Route::resource('/contact', ContactController::class);
//  Route::post('/contact', [ContactController::class, 'sendEmail'])->name('send.email');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::middleware(['auth', 'role:admin'])->group(function(){
        Route::get('/admin/dashboard',[AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    });
});

// FAQ:
Route::resource('faqs',FaqController::class);

Route::resource('Brands' , BrandController::class);
Route::resource('Color', ColorController::class);
Route::resource('sizes', SizeController::class);

// slide:
Route::resource('slides',slideController::class);

// Banners:
Route::resource('banners', BannerController::class);

// Informations:
Route::resource('infos', InfosController::class);



// Newsletter :
Route::get('/email', [App\Http\Controllers\EmailController::class, 'create']);
Route::post('/email', [App\Http\Controllers\EmailController::class, 'sendEmail'])->name('send.email');

require __DIR__.'/auth.php';
