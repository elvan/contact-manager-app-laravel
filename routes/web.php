<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', WelcomeController::class);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class);

    Route::get('/settings/profile-information', ProfileController::class)->name('user-profile-information.edit');

    Route::get('/settings/password', PasswordController::class)->name('user-password.edit');

    Route::resource('/contacts', ContactController::class);

    Route::delete('/contacts/{contact}/restore', [ContactController::class, 'restore'])
        ->name('contacts.restore')
        ->withTrashed();

    Route::delete('/contacts/{contact}/force-delete', [ContactController::class, 'forceDelete'])
        ->name('contacts.force-delete')
        ->withTrashed();

    Route::resource('/companies', CompanyController::class);

    Route::delete('/companies/{company}/restore', [CompanyController::class, 'restore'])
        ->name('companies.restore')
        ->withTrashed();

    Route::delete('/companies/{company}/force-delete', [CompanyController::class, 'forceDelete'])
        ->name('companies.force-delete')
        ->withTrashed();
});


Route::get('/eagerload-multipe', function () {
    $users = User::with(['companies', 'contacts'])->get();

    foreach ($users as $user) {
        echo $user->name . ": ";
        echo $user->companies->count() . " companies, " . $user->contacts->count() . " contacts<br>";
    }
});

Route::get('/eagerload-nested', function () {
    $users = User::with(['companies', 'companies.contacts'])->get();
    foreach ($users as $user) {
        echo $user->name . "<br />";
        foreach ($user->companies as $company) {
            echo $company->name . " has " . $company->contacts->count() . " contacts<br />";
        }
        echo "<br />";
    }
});

Route::get('/eagerload-constraint', function () {
    $users = User::with(['companies' => function ($query) {
        $query->where('email', 'like', '%.org');
    }])->get();
    foreach ($users as $user) {
        echo $user->name . "<br />";
        foreach ($user->companies as $company) {
            echo $company->email . "<br />";
        }
        echo "<br />";
    }
});

Route::get('/eagerload-lazy', function () {
    $users = User::get();
    $users->load(['companies' => function ($query) {
        $query->orderBy('name');
    }]);
    foreach ($users as $user) {
        echo $user->name . "<br />";
        foreach ($user->companies as $company) {
            echo $company->name . "<br />";
        }
        echo "<br />";
    }
});

Route::get('/eagerload-default', function () {
    $users = User::get();
    foreach ($users as $user) {
        echo $user->name . "<br />";
        foreach ($user->companies as $company) {
            echo $company->email . "<br />";
        }
        echo "<br />";
    }
});

Route::get('/count-models', function () {
    $users = User::get();
    $users->loadCount(['companies' => function ($query) {
        $query->where('email', 'like', '%@gmail.com');
    }]);
    foreach ($users as $user) {
        echo $user->name . "<br />";
        echo $user->companies_count . " companies<br />";
        echo "<br />";
    }
});
