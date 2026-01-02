<?php

namespace App\Console\Commands;

use App\Jobs\SendTimerExpiredAlertJob;
use App\Models\Trip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredTripsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trips:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired trips and send emergency alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find all active trips that have expired MORE THAN 60 SECONDS AGO
        // This gives the user 60s on the mobile app to enter their PIN and cancel the alert
        $expiredTrips = Trip::where('status', 'active')
            ->where('expected_end_time', '<=', now()->subSeconds(60))
            ->with('user')
            ->get();

        if ($expiredTrips->isEmpty()) {
            $this->info('No expired trips found.');
            return 0;
        }

        $this->info("Found {$expiredTrips->count()} expired trip(s).");

        foreach ($expiredTrips as $trip) {
            try {
                // Update status FIRST to prevent duplicate alerts
                $trip->update([
                    'status' => 'alerted',
                    'actual_end_time' => now(),
                ]);

                // Dispatch alert job
                dispatch(new SendTimerExpiredAlertJob($trip, $trip->user));

                $this->warn("Alert dispatched for trip #{$trip->id} (User: {$trip->user->full_name})");

                Log::warning("Timer expired alert dispatched", [
                    'trip_id' => $trip->id,
                    'user_id' => $trip->user->id,
                    'expected_end_time' => $trip->expected_end_time->toISOString(),
                ]);

            } catch (\Exception $e) {
                $this->error("Failed to process trip #{$trip->id}: {$e->getMessage()}");
                Log::error("Failed to process expired trip {$trip->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$expiredTrips->count()} expired trip(s) successfully.");
        return 0;
    }
}
