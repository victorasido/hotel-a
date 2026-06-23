<?php

namespace Database\Seeders;

use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'STD',
                'name' => 'Standard',
                'description' => 'Kamar standar nyaman dengan fasilitas lengkap',
                'base_price' => 350000,
                'capacity' => 2,
                'facilities' => 'AC, TV, WiFi, Kamar Mandi Dalam',
            ],
            [
                'code' => 'SUP',
                'name' => 'Superior',
                'description' => 'Kamar superior dengan pemandangan lebih baik dan fasilitas tambahan',
                'base_price' => 500000,
                'capacity' => 2,
                'facilities' => 'AC, TV LED, WiFi, Kamar Mandi Dalam, Bathtub, Minibar',
            ],
            [
                'code' => 'DLX',
                'name' => 'Deluxe',
                'description' => 'Kamar deluxe luas dengan dekorasi premium dan pemandangan terbaik',
                'base_price' => 750000,
                'capacity' => 3,
                'facilities' => 'AC, TV LED 43", WiFi, Kamar Mandi Dalam, Bathtub, Minibar, Balkon',
            ],
            [
                'code' => 'STE',
                'name' => 'Suite',
                'description' => 'Suite mewah dengan ruang tamu terpisah dan fasilitas eksklusif',
                'base_price' => 1500000,
                'capacity' => 4,
                'facilities' => 'AC, TV LED 55", WiFi, Kamar Mandi Premium, Jacuzzi, Minibar, Ruang Tamu, Dapur Kecil, Butler Service',
            ],
        ];

        foreach ($types as $type) {
            RoomType::create($type);
        }

        // Create 20 rooms across 4 floors
        $statuses = ['VC', 'VC', 'VC', 'VD', 'OC', 'OC', 'OD', 'OOO'];
        $roomTypeIds = RoomType::all()->pluck('id')->toArray();

        $roomData = [
            // Floor 1 - Standard
            ['room_number' => '101', 'floor' => 1, 'room_type_id' => $roomTypeIds[0]],
            ['room_number' => '102', 'floor' => 1, 'room_type_id' => $roomTypeIds[0]],
            ['room_number' => '103', 'floor' => 1, 'room_type_id' => $roomTypeIds[0]],
            ['room_number' => '104', 'floor' => 1, 'room_type_id' => $roomTypeIds[0]],
            ['room_number' => '105', 'floor' => 1, 'room_type_id' => $roomTypeIds[0]],
            // Floor 2 - Standard & Superior
            ['room_number' => '201', 'floor' => 2, 'room_type_id' => $roomTypeIds[0]],
            ['room_number' => '202', 'floor' => 2, 'room_type_id' => $roomTypeIds[1]],
            ['room_number' => '203', 'floor' => 2, 'room_type_id' => $roomTypeIds[1]],
            ['room_number' => '204', 'floor' => 2, 'room_type_id' => $roomTypeIds[1]],
            ['room_number' => '205', 'floor' => 2, 'room_type_id' => $roomTypeIds[1]],
            // Floor 3 - Superior & Deluxe
            ['room_number' => '301', 'floor' => 3, 'room_type_id' => $roomTypeIds[1]],
            ['room_number' => '302', 'floor' => 3, 'room_type_id' => $roomTypeIds[2]],
            ['room_number' => '303', 'floor' => 3, 'room_type_id' => $roomTypeIds[2]],
            ['room_number' => '304', 'floor' => 3, 'room_type_id' => $roomTypeIds[2]],
            ['room_number' => '305', 'floor' => 3, 'room_type_id' => $roomTypeIds[2]],
            // Floor 4 - Deluxe & Suite
            ['room_number' => '401', 'floor' => 4, 'room_type_id' => $roomTypeIds[2]],
            ['room_number' => '402', 'floor' => 4, 'room_type_id' => $roomTypeIds[3]],
            ['room_number' => '403', 'floor' => 4, 'room_type_id' => $roomTypeIds[3]],
            ['room_number' => '404', 'floor' => 4, 'room_type_id' => $roomTypeIds[3]],
            ['room_number' => '405', 'floor' => 4, 'room_type_id' => $roomTypeIds[3]],
        ];

        foreach ($roomData as $i => $room) {
            $room['status'] = $statuses[$i % count($statuses)];
            Room::create($room);
        }
    }
}
