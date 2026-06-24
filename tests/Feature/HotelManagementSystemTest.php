<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\GuestFolio;
use App\Models\FolioItem;
use App\Models\FnbCategory;
use App\Models\FnbMenu;
use App\Models\FnbOrder;
use App\Models\HousekeepingTask;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HotelManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $frontOffice;
    private User $fnb;
    private User $housekeeping;
    private RoomType $roomType;
    private Room $room101;
    private Room $room102;

    protected function setUp(): void
    {
        parent::setUp();

        // Run roles and permissions seeder
        $this->seed(RolesAndPermissionsSeeder::class);

        // Create standard Room Type and Rooms
        $this->roomType = RoomType::create([
            'name' => 'Deluxe Room',
            'code' => 'DLX',
            'base_price' => 500000.00,
            'is_active' => true,
        ]);

        $this->room101 = Room::create([
            'room_number' => '101',
            'room_type_id' => $this->roomType->id,
            'status' => 'VC', // Vacant Clean
            'floor' => 1,
            'is_active' => true,
        ]);

        $this->room102 = Room::create([
            'room_number' => '102',
            'room_type_id' => $this->roomType->id,
            'status' => 'VC',
            'floor' => 1,
            'is_active' => true,
        ]);

        // Create Users for each role
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@hotel.com',
            'password' => bcrypt('password'),
        ]);
        $this->admin->assignRole('Super Admin');

        $this->frontOffice = User::create([
            'name' => 'FO User',
            'email' => 'fo@hotel.com',
            'password' => bcrypt('password'),
        ]);
        $this->frontOffice->assignRole('Front Office');

        $this->fnb = User::create([
            'name' => 'Fnb User',
            'email' => 'fnb@hotel.com',
            'password' => bcrypt('password'),
        ]);
        $this->fnb->assignRole('FnB');

        $this->housekeeping = User::create([
            'name' => 'Housekeeping User',
            'email' => 'hk@hotel.com',
            'password' => bcrypt('password'),
        ]);
        $this->housekeeping->assignRole('Housekeeping');
    }

    /**
     * Test authentication & authorization redirection.
     */
    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test role-based dashboard access.
     */
    public function test_authenticated_users_can_access_dashboard()
    {
        $this->actingAs($this->frontOffice);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $this->actingAs($this->fnb);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Test role-based route access controls.
     */
    public function test_role_based_route_access_controls()
    {
        // 1. Super Admin has access to everything
        $this->actingAs($this->admin);
        $this->get('/master/rooms')->assertStatus(200);
        $this->get('/room-status')->assertStatus(200);
        $this->get('/fnb/orders')->assertStatus(200);
        $this->get('/housekeeping/tasks')->assertStatus(200);

        // 2. Front Office can access room-status but NOT master/rooms or housekeeping
        $this->actingAs($this->frontOffice);
        $this->get('/room-status')->assertStatus(200);
        $this->get('/master/rooms')->assertStatus(403);
        $this->get('/housekeeping/tasks')->assertStatus(403);

        // 3. FnB can access fnb/orders but NOT master/rooms, room-status, or housekeeping
        $this->actingAs($this->fnb);
        $this->get('/fnb/orders')->assertStatus(200);
        $this->get('/master/rooms')->assertStatus(403);
        $this->get('/room-status')->assertStatus(403);
        $this->get('/housekeeping/tasks')->assertStatus(403);

        // 4. Housekeeping can access housekeeping/tasks but NOT master/rooms, room-status, or fnb/orders
        $this->actingAs($this->housekeeping);
        $this->get('/housekeeping/tasks')->assertStatus(200);
        $this->get('/master/rooms')->assertStatus(403);
        $this->get('/room-status')->assertStatus(403);
    }

    /**
     * Test Guest and Reservation creation via Livewire.
     */
    public function test_guest_and_reservation_creation_flow()
    {
        $this->actingAs($this->frontOffice);

        // 1. Check guest creation
        $guest = Guest::create([
            'name' => 'John Doe',
            'id_card_type' => 'KTP',
            'id_card_number' => '1234567890',
            'phone' => '08123456789',
            'nationality' => 'Indonesia',
        ]);

        $this->assertDatabaseHas('guests', ['name' => 'John Doe']);

        // 2. Test Reservation Form Component
        $checkInDate = now()->addDays(1)->format('Y-m-d');
        $checkOutDate = now()->addDays(3)->format('Y-m-d'); // 2 nights

        Livewire::test(\App\Livewire\Reservations\ReservationForm::class)
            ->set('guest_id', $guest->id)
            ->set('room_type_id', $this->roomType->id)
            ->set('room_id', $this->room101->id)
            ->set('check_in_date', $checkInDate)
            ->set('check_out_date', $checkOutDate)
            ->assertSet('nights', 2)
            ->assertSet('total_amount', 1000000.00)
            ->call('save')
            ->assertRedirect(route('reservations.index'));

        $this->assertDatabaseHas('reservations', [
            'guest_id' => $guest->id,
            'room_id' => $this->room101->id,
            'total_amount' => 1000000.00,
            'status' => 'confirmed'
        ]);
    }

    /**
     * Test complete check-in, folio, F&B order, kitchen serving, check-out, and housekeeping flow.
     */
    public function test_complete_hotel_lifecycle_flow()
    {
        // 1. Setup a Guest & Reservation
        $guest = Guest::create([
            'name' => 'Jane Smith',
            'id_card_type' => 'Passport',
            'id_card_number' => 'PP-998877',
            'phone' => '08198765432',
            'nationality' => 'Foreigner',
        ]);

        $reservation = Reservation::create([
            'booking_code' => 'RSV-TEST-001',
            'guest_id' => $guest->id,
            'room_id' => $this->room101->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(), // 2 nights
            'pax' => 2,
            'status' => 'confirmed',
            'source' => 'walk_in',
            'room_rate' => 500000.00,
            'total_amount' => 1000000.00,
            'created_by' => $this->frontOffice->id,
        ]);

        // 2. Perform Check-In
        $this->actingAs($this->frontOffice);

        Livewire::test(\App\Livewire\FrontDesk\CheckIn::class, ['reservation' => $reservation])
            ->set('notes', 'Check-in test')
            ->call('processCheckIn');

        // Assert room status is Occupied (OC), Reservation is checked_in, and Folio is created
        $this->assertEquals('OC', $this->room101->fresh()->status);
        $this->assertEquals('checked_in', $reservation->fresh()->status);

        $checkIn = CheckIn::where('reservation_id', $reservation->id)->first();
        $this->assertNotNull($checkIn);

        $folio = GuestFolio::where('check_in_id', $checkIn->id)->first();
        $this->assertNotNull($folio);
        $this->assertEquals('open', $folio->status);
        $this->assertEquals(1000000.00, $folio->grand_total); // Room charge

        // 3. Add Extra Item to Folio
        Livewire::test(\App\Livewire\Billing\FolioDetail::class, ['folio' => $folio])
            ->set('item_type', 'extra')
            ->set('item_description', 'Extra Bed')
            ->set('item_qty', 1)
            ->set('item_unit_price', '150000')
            ->call('addItem');

        $this->assertEquals(1150000.00, $folio->fresh()->grand_total);

        // 4. Create F&B Service order (Room Service)
        $this->actingAs($this->fnb);

        $fnbCat = FnbCategory::create(['name' => 'Makanan', 'sort_order' => 1, 'is_active' => true]);
        $menuItem = FnbMenu::create([
            'category_id' => $fnbCat->id,
            'name' => 'Nasi Goreng Nusantara',
            'price' => 50000.00,
            'is_available' => true,
        ]);

        Livewire::test(\App\Livewire\Fnb\OrderForm::class)
            ->set('room_id', $this->room101->id)
            ->set('order_type', 'room_service')
            ->call('addToCart', $menuItem->id)
            ->call('submitOrder')
            ->assertRedirect(route('fnb.orders'));

        // Check if F&B order is created
        $order = FnbOrder::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(50000.00, $order->total);

        // Check if F&B order was automatically added to guest folio
        $this->assertEquals(1200000.00, $folio->fresh()->grand_total);

        // 5. Kitchen Display process the F&B order
        Livewire::test(\App\Livewire\Fnb\KitchenDisplay::class)
            ->call('updateStatus', $order->id, 'processing')
            ->call('updateStatus', $order->id, 'served');

        $this->assertEquals('served', $order->fresh()->status);

        // 6. Perform Check-Out
        $this->actingAs($this->frontOffice);

        Livewire::test(\App\Livewire\FrontDesk\CheckOut::class, ['checkIn' => $checkIn])
            ->set('payment_method', 'cash')
            ->set('notes', 'Checked out OK')
            ->call('processCheckOut')
            ->assertRedirect(route('reservations.index'));

        // Assert Folio is closed, Room status is VD (Vacant Dirty), Reservation is checked_out
        $this->assertEquals('closed', $folio->fresh()->status);
        $this->assertEquals('VD', $this->room101->fresh()->status);
        $this->assertEquals('checked_out', $reservation->fresh()->status);

        // Check that a Housekeeping Task was automatically generated
        $hkTask = HousekeepingTask::where('room_id', $this->room101->id)->latest()->first();
        $this->assertNotNull($hkTask);
        $this->assertEquals('pending', $hkTask->status);
        $this->assertEquals('cleaning', $hkTask->task_type);

        // 7. Housekeeping complete the task
        $this->actingAs($this->housekeeping);

        Livewire::test(\App\Livewire\Housekeeping\TaskBoard::class)
            ->call('moveTask', $hkTask->id, 'in_progress')
            ->call('moveTask', $hkTask->id, 'done');

        // Assert task status is done and Room status has reverted to VC (Vacant Clean)
        $this->assertEquals('done', $hkTask->fresh()->status);
        $this->assertEquals('VC', $this->room101->fresh()->status);
    }
}
