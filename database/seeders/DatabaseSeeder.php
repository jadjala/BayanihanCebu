<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\IncidentReport;
use App\Models\Donation;
use App\Models\DonationLog;
use App\Models\Notification;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@bayanihan.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'contact_info' => '+63 912 345 6789',
        ]);

        // Create officials
        $officials = User::factory()
            ->official()
            ->count(3)
            ->create();

        $official1 = User::create([
            'name' => 'Barangay Captain',
            'email' => 'captain@bayanihan.test',
            'password' => bcrypt('password'),
            'role' => 'official',
            'contact_info' => '+63 912 345 6780',
        ]);

        // Create residents
        $residents = User::factory()
            ->resident()
            ->count(10)
            ->create();

        $resident1 = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'resident@bayanihan.test',
            'password' => bcrypt('password'),
            'role' => 'resident',
            'contact_info' => '+63 912 345 6781',
        ]);

        // Create donors
        $donors = User::factory()
            ->donor()
            ->count(5)
            ->create();

        $donor1 = User::create([
            'name' => 'Maria Santos',
            'email' => 'donor@bayanihan.test',
            'password' => bcrypt('password'),
            'role' => 'donor',
            'contact_info' => '+63 912 345 6782',
        ]);

        // Create incident reports
        // Pending reports
        IncidentReport::factory()
            ->count(5)
            ->pending()
            ->create([
                'user_id' => $residents->random()->id,
            ]);

        // Verified reports
        IncidentReport::factory()
            ->count(8)
            ->verified()
            ->create([
                'user_id' => $residents->random()->id,
                'verified_by' => $officials->random()->id,
            ]);

        // Resolved reports
        IncidentReport::factory()
            ->count(12)
            ->resolved()
            ->create([
                'user_id' => $residents->random()->id,
                'verified_by' => $officials->random()->id,
            ]);

        // Create some critical reports
        IncidentReport::create([
            'user_id' => $resident1->id,
            'title' => 'Fire Emergency at Purok 5',
            'description' => 'There is a severe fire burning at Purok 5. Multiple houses are affected. Emergency response needed immediately!',
            'category' => 'Fire',
            'location' => 'Purok 5, Brgy. San Isidro',
            'urgency_level' => 'Critical',
            'status' => 'Pending',
        ]);

        // Create donations
        // Pending donations
        Donation::factory()
            ->count(3)
            ->pending()
            ->create([
                'donor_id' => $donors->random()->id,
            ]);

        // Verified donations
        $verifiedDonations = Donation::factory()
            ->count(5)
            ->verified()
            ->create([
                'donor_id' => $donors->random()->id,
                'verified_by' => $officials->random()->id,
            ]);

        // Distributed donations with logs
        $distributedDonations = Donation::factory()
            ->count(4)
            ->distributed()
            ->create([
                'donor_id' => $donors->random()->id,
                'verified_by' => $officials->random()->id,
            ]);

        // Create distribution logs for distributed donations
        foreach ($distributedDonations as $donation) {
            $totalDistributed = 0;
            $numLogs = rand(1, 3);

            for ($i = 0; $i < $numLogs; $i++) {
                $quantityToDistribute = $donation->quantity / $numLogs;
                
                DonationLog::create([
                    'donation_id' => $donation->id,
                    'official_id' => $officials->random()->id,
                    'distributed_to' => fake()->name() . ' - ' . fake()->randomElement(['Purok 1', 'Purok 2', 'Fire Victims']),
                    'quantity_distributed' => $quantityToDistribute,
                    'remarks' => fake()->optional()->sentence(),
                ]);

                $totalDistributed += $quantityToDistribute;
            }
        }

        // Create some notifications
        foreach ($residents->take(5) as $resident) {
            Notification::create([
                'user_id' => $resident->id,
                'type' => 'system_alert',
                'title' => 'Welcome to BAYANIHAN',
                'message' => 'Thank you for registering. You can now submit incident reports and track barangay activities.',
                'read_status' => 'unread',
            ]);
        }

        foreach ($donors->take(3) as $donor) {
            Notification::create([
                'user_id' => $donor->id,
                'type' => 'donation_verified',
                'title' => 'Donation Verified',
                'message' => 'Your donation has been verified by barangay officials. Thank you for your contribution!',
                'read_status' => 'unread',
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@bayanihan.test / password');
        $this->command->info('Official: captain@bayanihan.test / password');
        $this->command->info('Resident: resident@bayanihan.test / password');
        $this->command->info('Donor: donor@bayanihan.test / password');
    }
}