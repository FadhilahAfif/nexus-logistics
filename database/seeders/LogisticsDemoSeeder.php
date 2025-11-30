<?php

namespace Database\Seeders;

use App\Models\Shipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LogisticsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            ['Jakarta', 'Bandung', 'Semarang', 'Surabaya'],
            ['Medan', 'Pekanbaru', 'Palembang', 'Jakarta'],
            ['Denpasar', 'Mataram', 'Makassar', 'Manado'],
        ];

        Shipment::factory()
            ->count(12)
            ->create()
            ->each(function (Shipment $shipment) use ($routes): void {
                $timeline = collect($routes)->random();
                $start = Carbon::now()->subDays(rand(1, 5));
                $statuses = ['picked_up', 'in_transit', 'delivered'];

                foreach ($timeline as $index => $city) {
                    $status = $statuses[$index] ?? 'in_transit';

                    $shipment->updates()->create([
                        'location' => $city,
                        'description' => 'Paket telah sampai di ' . $city,
                        'status' => $status,
                        'happened_at' => (clone $start)->addHours($index * rand(3, 8)),
                    ]);

                    if ($status === 'delivered') {
                        $shipment->status = 'delivered';
                        $shipment->save();
                    }
                }
            });
    }
}

