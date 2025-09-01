<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Applications\NationalHoliday\Model\NationalHoliday;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Applications\Country\Model\Country;

class PopulateNationalHolidays extends Command
{
    protected $signature = 'populate:holidays {--year=} {--drop}';
    protected $description = 'Populate national holidays for countries in the database using Nager.Date API';

    public function handle()
    {
        $year = $this->option('year') ?? now()->year;
        $dropFlag = $this->option('drop');

        if ($dropFlag) {
            NationalHoliday::truncate();
        }

        if (!$year) {
            return Command::FAILURE;
        }

        $this->info("Fetching holidays for year: $year");

        // Get all countries from the database
        $countries = Country::all();

        if ($countries->isEmpty()) {
            $this->error("No countries found in the database. Please add countries before running this command.");
            return Command::FAILURE;
        }

        $client = new Client();

        foreach ($countries as $country) {
            if (empty($country->country_code)) {
                $this->warn("Skipping country '{$country->name}' due to missing country code.");
                continue;
            }

            $this->info("Fetching holidays for {$country->name} ({$country->country_code})...");

            try {
                $response = $client->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/{$country->country_code}");

                if ($response->getStatusCode() !== 200) {
                    $this->error("Failed to fetch holidays for {$country->name}.");
                    continue;
                }

                $holidays = json_decode($response->getBody(), true);

                foreach ($holidays as $holiday) {
                    $holidayDate = Carbon::parse($holiday['date']);

                    // Exclude weekends
                    if ($holidayDate->isWeekend()) {
                        continue;
                    }

                    // Insert or update the holiday
                    NationalHoliday::updateOrCreate(
                        [
                            'date'    => $holiday['date'],
                            'country' => $country->name, // Save the country name
                            'year'    => $year,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                $this->info("Holidays successfully populated for {$country->name}.");
            } catch (\Exception $e) {
                $this->error("Error fetching holidays for {$country->name}: " . $e->getMessage());
            }
        }

        $this->info("✅ All holidays populated successfully.");
        return Command::SUCCESS;
    }
}
