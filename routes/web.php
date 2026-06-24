<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\RoomStatusBoard;
use App\Livewire\MasterData\RoomTypes;
use App\Livewire\MasterData\Rooms;
use App\Livewire\MasterData\Users;
use App\Livewire\MasterData\SettingsPortal;
use App\Livewire\Guests\GuestList;
use App\Livewire\Reservations\ReservationList;
use App\Livewire\Reservations\ReservationForm;
use App\Livewire\Reservations\ReservationDetail;
use App\Livewire\Reservations\ReservationsPortal;
use App\Livewire\FrontDesk\CheckIn;
use App\Livewire\FrontDesk\CheckOut;
use App\Livewire\Billing\FolioDetail;
use App\Livewire\Fnb\MenuManagement;
use App\Livewire\Fnb\OrderList;
use App\Livewire\Fnb\OrderForm;
use App\Livewire\Fnb\KitchenDisplay;
use App\Livewire\Fnb\FnbPortal;
use App\Livewire\Housekeeping\TaskBoard;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AuthController;

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login-post', [AuthController::class, 'loginPost'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {

    // ── Dashboard (Semua Role)
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // ── Pengaturan Hotel — Portal Master Data (Super Admin only)
    Route::middleware('role:Super Admin')->group(function () {
        Route::get('/settings', SettingsPortal::class)->name('settings.portal');
        // Rute individual dipertahankan (digunakan sub-component dalam portal)
        Route::prefix('master')->name('master.')->group(function () {
            Route::get('/room-types', RoomTypes::class)->name('room-types');
            Route::get('/rooms', Rooms::class)->name('rooms');
            Route::get('/users', Users::class)->name('users');
        });
    });

    // ── Reservasi & Tamu — Portal (Super Admin & Front Office)
    Route::middleware('role:Super Admin|Front Office')->group(function () {
        Route::get('/room-status', RoomStatusBoard::class)->name('room-status');
        Route::get('/guests', GuestList::class)->name('guests');

        // Portal gabungan Reservasi + Tamu
        Route::get('/reservations', ReservationsPortal::class)->name('reservations.portal');

        // Rute penunjang Reservasi (tetap dipertahankan untuk deep-link & form)
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/list',              ReservationList::class)->name('index');
            Route::get('/create',            ReservationForm::class)->name('create');
            Route::get('/{reservation}/edit', ReservationForm::class)->name('edit');
            Route::get('/{reservation}',      ReservationDetail::class)->name('show');
        });

        Route::get('/check-in/{reservation}', CheckIn::class)->name('check-in');
        Route::get('/check-out/{checkIn}',    CheckOut::class)->name('check-out');
        Route::get('/folio/{folio}',          FolioDetail::class)->name('folio');
        Route::get('/invoice/{folio}/pdf',    [InvoiceController::class, 'download'])->name('invoice.pdf');
    });

    // ── Layanan F&B — Portal (Super Admin, FnB, Front Office)
    Route::middleware('role:Super Admin|FnB|Front Office')->group(function () {
        Route::get('/fnb', FnbPortal::class)->name('fnb.portal');
        // Rute penunjang F&B (deep-link & form order baru)
        Route::prefix('fnb')->name('fnb.')->group(function () {
            Route::get('/orders',        OrderList::class)->name('orders');
            Route::get('/orders/create', OrderForm::class)->name('orders.create');
        });
    });

    Route::middleware('role:Super Admin|FnB')->group(function () {
        Route::prefix('fnb')->name('fnb.')->group(function () {
            Route::get('/menu',    MenuManagement::class)->name('menu');
            Route::get('/kitchen', KitchenDisplay::class)->name('kitchen');
        });
    });

    // ── Housekeeping (Super Admin & Housekeeping)
    Route::middleware('role:Super Admin|Housekeeping')->group(function () {
        Route::get('/housekeeping/tasks', TaskBoard::class)->name('housekeeping.tasks');
    });
});

