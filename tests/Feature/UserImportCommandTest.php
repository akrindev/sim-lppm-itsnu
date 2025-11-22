<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class UserImportCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'dosen']);
        Role::create(['name' => 'mahasiswa']);
    }

    public function test_artisan_command_imports_users()
    {
        // Mock Excel facade
        Excel::shouldReceive('toArray')
            ->andReturn([[
                ['name' => 'Command User', 'email' => 'command@example.com', 'password' => 'password', 'nidn' => '99999', 'type' => 'dosen', 'inst' => 'INST', 'prodi' => 'PRODI', 'sinta' => '999999', 'address' => 'Jl. Command', 'birthdate' => '1990-01-01', 'birthplace' => 'Jakarta'],
            ]]);

        Excel::shouldReceive('import')
            ->once();

        // Create a dummy file to pass existence check
        $file = sys_get_temp_dir().'/test_import.xlsx';
        touch($file);

        $this->artisan('users:import', ['file' => $file])
            ->expectsOutput('Reading file: '.$file)
            ->expectsConfirmation('Do you want to import these users?', 'yes')
            ->expectsOutput('Import completed successfully.')
            ->assertExitCode(0);

        // Since we mock import, we can't check DB here unless we use a real import.
        // But for unit testing the command interaction, this is sufficient.
        // To test actual import logic, we should test the UsersImport class separately or use a real file.

        unlink($file);
    }
}
