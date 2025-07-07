<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $batchSize = 1000;
        $totalBookings = 1000000;
        $startDate = Carbon::create(2025, 1, 1);

        for ($i = 0; $i < $totalBookings / $batchSize; $i++) {
            $bookings = [];

            for ($j = 0; $j < $batchSize; $j++) {
                $date = $startDate->copy()->addDays(rand(0, 365));
                $bookingType = ['full_day', 'half_day', 'custom'][rand(0, 2)];

                $slot = $bookingType === 'half_day' ? ['first_half', 'second_half'][rand(0, 1)] : null;
                $from = $bookingType === 'custom' ? Carbon::createFromTime(rand(8, 17), 0, 0)->format('H:i') : null;
                $to = $bookingType === 'custom' ? Carbon::parse($from)->addHour()->format('H:i') : null;

                $bookings[] = [
                    'user_id' => 1,
                    'customer_name' => 'Test User',
                    'customer_email' => Str::random(5) . '@mail.com',
                    'booking_start_date' => $date->toDateString(),
                    'booking_end_date' => $date->toDateString(),
                    'booking_type' => $bookingType,
                    'booking_slot' => $slot,
                    'booking_from' => $from,
                    'booking_to' => $to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Booking::insert($bookings);
            echo "Inserted " . ($i + 1) * $batchSize . " records\n";
        }
    }
}
