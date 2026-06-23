<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\RoomStatusBoard;
use App\Livewire\MasterData\RoomTypes;
use App\Livewire\MasterData\Rooms;
use App\Livewire\MasterData\Users;
use App\Livewire\Guests\GuestList;
use App\Livewire\Reservations\ReservationList;
use App\Livewire\Reservations\ReservationForm;
use App\Livewire\Reservations\ReservationDetail;
use App\Livewire\FrontDesk\CheckIn;
use App\Livewire\FrontDesk\CheckOut;
use App\Livewire\Billing\FolioDetail;
use App\Livewire\Fnb\MenuManagement;
use App\Livewire\Fnb\OrderList;
use App\Livewire\Fnb\OrderForm;
use App\Livewire\Fnb\KitchenDisplay;
use App\Livewire\Housekeeping\TaskBoard;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AuthController;

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/room-status', RoomStatusBoard::class)->name('room-status');

    // Master Data (Super Admin only)
    Route::prefix('master')->name('master.')->middleware('role:Super Admin')->group(function () {
        Route::get('/room-types', RoomTypes::class)->name('room-types');
        Route::get('/rooms', Rooms::class)->name('rooms');
        Route::get('/users', Users::class)->name('users');
    });

    // Guest Management
    Route::get('/guests', GuestList::class)->name('guests');

    // Reservations
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', ReservationList::class)->name('index');
        Route::get('/create', ReservationForm::class)->name('create');
        Route::get('/{reservation}/edit', ReservationForm::class)->name('edit');
        Route::get('/{reservation}', ReservationDetail::class)->name('show');
    });

    // Front Desk
    Route::get('/check-in/{reservation}', CheckIn::class)->name('check-in');
    Route::get('/check-out/{checkIn}', CheckOut::class)->name('check-out');

    // Billing / Folio
    Route::get('/folio/{folio}', FolioDetail::class)->name('folio');
    Route::get('/invoice/{folio}/pdf', [InvoiceController::class, 'download'])->name('invoice.pdf');

    // F&B
    Route::prefix('fnb')->name('fnb.')->group(function () {
        Route::get('/menu', MenuManagement::class)->name('menu');
        Route::get('/orders', OrderList::class)->name('orders');
        Route::get('/orders/create', OrderForm::class)->name('orders.create');
        Route::get('/kitchen', KitchenDisplay::class)->name('kitchen');
    });

    // Housekeeping
    Route::prefix('housekeeping')->name('housekeeping.')->group(function () {
        Route::get('/tasks', TaskBoard::class)->name('tasks');
    });
});
