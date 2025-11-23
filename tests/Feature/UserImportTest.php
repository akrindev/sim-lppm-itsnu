<?php

namespace Tests\Feature;

use App\Livewire\Users\Import;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class UserImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'admin lppm']);
        Role::create(['name' => 'dosen']);
        Role::create(['name' => 'mahasiswa']);
    }

    public function test_admin_can_access_import_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin lppm');

        $this->actingAs($admin)
            ->get(route('users.import'))
            ->assertSuccessful();
    }

    public function test_non_admin_cannot_access_import_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.import'))
            ->assertForbidden();
    }

    public function test_can_upload_and_parse_excel_file()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin lppm');

        // Mock Excel facade to return data
        // Note: When using Excel::toArray(new Import, file), the mock needs to match that signature
        Excel::shouldReceive('toArray')
            ->andReturn([[
                ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password', 'nidn' => '12345', 'type' => 'dosen', 'inst' => 'INST', 'prodi' => 'PRODI', 'sinta' => '123456', 'address' => 'Jl. Test', 'birthdate' => '1990-01-01', 'birthplace' => 'Surabaya'],
            ]]);

        // Also mock rules/customValidationMessages if called directly, but in component we call them on instance.
        // Since we instantiate UsersImport in component, we might need to rely on real object or mock injection if possible.
        // However, Livewire test with real Excel object is complex.
        // For simplicity in this test refactor, we'll assume the component uses the real UsersImport class
        // and we just mock the Excel::toArray part which reads the file.

        // But wait, Excel::toArray is called with an instance.
        // Excel::shouldReceive('toArray')->with(\Mockery::type(\App\Imports\UsersImport::class), ...)->andReturn(...)

        $file = UploadedFile::fake()->create('users.xlsx');

        Livewire::actingAs($admin)
            ->test(Import::class)
            ->set('file', $file)
            ->assertSet('isPreviewing', true)
            ->assertSet('parsedData.0.email', 'john@example.com');
    }

    public function test_can_import_users()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin lppm');

        Excel::shouldReceive('toArray')
            ->andReturn([[
                ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password', 'nidn' => '12345', 'type' => 'dosen', 'inst' => 'INST', 'prodi' => 'PRODI', 'sinta' => '123456', 'address' => 'Jl. Test', 'birthdate' => '1990-01-01', 'birthplace' => 'Surabaya'],
            ]]);

        Excel::shouldReceive('import')
            ->once();

        $file = UploadedFile::fake()->create('users.xlsx');

        Livewire::actingAs($admin)
            ->test(Import::class)
            ->set('file', $file)
            ->call('import')
            ->assertDispatched('notify')
            ->assertRedirect(route('users.index'));
    }
}
