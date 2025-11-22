<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import {file : Path to the Excel file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from an Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (! file_exists($file)) {
            $this->error("File not found: $file");

            return 1;
        }

        $this->info("Reading file: $file");

        try {
            $import = new \App\Imports\UsersImport;

            // Preview
            $rows = Excel::toArray($import, $file)[0];
            $this->info('Found '.count($rows).' rows.');

            if (! $this->confirm('Do you want to import these users?')) {
                $this->info('Import cancelled.');

                return 0;
            }

            $this->info('Importing...');

            Excel::import($import, $file);

            $this->info('Import completed successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $this->error('Validation failed:');
            foreach ($e->failures() as $failure) {
                $this->line('Row '.$failure->row().': '.implode(', ', $failure->errors()));
            }

            return 1;
        } catch (\Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
