<?php

use App\Models\Area;
use App\Models\File;
use App\Models\User;
use App\Models\College;
use App\Models\Program;
use App\Models\Accreditor;
use App\Models\AccreditationEvent;
use Spatie\Permission\Models\Role;
use App\Http\Middleware\CheckRoleStatus;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'taskforce', 'guard_name' => 'web']);
    Role::create(['name' => 'ido_staff', 'guard_name' => 'web']);
    Role::create(['name' => 'college_officer', 'guard_name' => 'web']);

    // Create default Colleges and Programs to prevent FK constraints
    College::create(['id' => 1, 'code' => 'CAS', 'name' => 'College of Arts and Sciences']);
    College::create(['id' => 2, 'code' => 'CEMDS', 'name' => 'College of Economics']);
    Program::create(['id' => 1, 'code' => 'BSCS', 'name' => 'BS Computer Science', 'college_id' => 1]);
    Program::create(['id' => 2, 'code' => 'BSBA', 'name' => 'BS Business Administration', 'college_id' => 2]);
    Area::create(['id' => 1, 'code' => 'A1', 'slug' => 'area-1', 'name' => 'Area I', 'order_no' => 1]);
    User::factory()->create(['id' => 1, 'college_id' => 1]);
});

test('admin can see all files', function () {
    $admin = User::factory()->create(['college_id' => 1]);
    $admin->assignRole('admin');

    File::factory()->create(['is_general' => true]);
    File::factory()->create(['is_general' => false]);

    $this->actingAs($admin);
    expect(File::accessibleBy($admin)->count())->toBe(2);
});

test('accreditor sees only assigned event files', function () {
    $event1 = AccreditationEvent::factory()->create();
    $event2 = AccreditationEvent::factory()->create();

    $accreditor = Accreditor::factory()->create([
        'college_id' => 1,
        'program_id' => 1,
        'created_by' => 1,
    ]);
    $accreditor->events()->attach($event1->id);

    $file1 = File::factory()->create();
    $file2 = File::factory()->create();

    // Share file1 to event1, area 1
    $event1->files()->attach($file1->id, ['area_id' => 1, 'shared_by' => 1]);
    // file2 is not shared or shared to event2
    $event2->files()->attach($file2->id, ['area_id' => 1, 'shared_by' => 1]);

    $this->actingAs($accreditor, 'accreditor');
    $accessibleFiles = File::accessibleBy($accreditor)->get();

    expect($accessibleFiles)->toHaveCount(1);
    expect($accessibleFiles->first()->id)->toBe($file1->id);
});

test('taskforce member sees their personal files and general college files', function () {
    $user = User::factory()->create(['college_id' => 1]);
    $user->assignRole('taskforce');

    $fileInCollege = File::factory()->create(['is_general' => true, 'college_id' => 1]);
    $fileInOtherCollege = File::factory()->create(['is_general' => true, 'college_id' => 2]);
    $myPersonalFile = File::factory()->create(['is_general' => false, 'uploaded_by' => $user->id]);
    $otherPersonalFile = File::factory()->create(['is_general' => false, 'college_id' => 2]);

    $this->actingAs($user);
    $accessibleFiles = File::accessibleBy($user)->get();

    expect($accessibleFiles)->toHaveCount(2);
    expect($accessibleFiles->pluck('id'))->toContain($fileInCollege->id, $myPersonalFile->id);
    expect($accessibleFiles->pluck('id'))->not->toContain($fileInOtherCollege->id, $otherPersonalFile->id);
});

test('upload as taskforce requires college_id and program_id', function () {
    $user = User::factory()->create([
        'college_id' => null,
        'program_id' => null,
    ]);
    $user->assignRole('taskforce');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    $response = $this->postJson(route('api.files.temp'), [
        'filename' => 'test.pdf',
        'metadata' => [
            'title' => 'Test File',
        ],
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['metadata.college_id', 'metadata.program_id']);
});

test('upload as taskforce succeeds when college_id and program_id are set', function () {
    $college = College::create(['code' => 'COE', 'name' => 'College of Engineering']);
    $program = Program::create(['code' => 'BSEE', 'name' => 'BS Electrical Engineering', 'college_id' => $college->id]);

    $user = User::factory()->create([
        'college_id' => $college->id,
        'program_id' => $program->id,
    ]);
    $user->assignRole('taskforce');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    $response = $this->postJson(route('api.files.temp'), [
        'filename' => 'test.pdf',
        'metadata' => [
            'title' => 'Test File',
        ],
    ]);

    $response->assertStatus(200);
});

test('upload as college officer requires college_id', function () {
    $user = User::factory()->create([
        'college_id' => null,
    ]);
    $user->assignRole('college_officer');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    $response = $this->postJson(route('api.files.temp'), [
        'filename' => 'test.pdf',
        'metadata' => [
            'title' => 'Test File',
        ],
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['metadata.college_id']);
});

test('upload as college officer succeeds when college_id is set', function () {
    $college = College::create(['code' => 'CON', 'name' => 'College of Nursing']);

    $user = User::factory()->create([
        'college_id' => $college->id,
    ]);
    $user->assignRole('college_officer');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    $response = $this->postJson(route('api.files.temp'), [
        'filename' => 'test.pdf',
        'metadata' => [
            'title' => 'Test File',
        ],
    ]);

    $response->assertStatus(200);
});

test('export csv report has correct columns', function () {
    $user = User::factory()->create(['college_id' => 1]);
    $user->assignRole('admin');

    $this->actingAs($user);

    $response = $this->get(route('files.export-report'));

    $response->assertStatus(200);
    
    $content = $response->streamedContent();
    $lines = explode("\n", $content);
    
    // Check if BOM is at the start, remove it if present
    $headerLine = $lines[0];
    if (str_starts_with($headerLine, "\xEF\xBB\xBF")) {
        $headerLine = substr($headerLine, 3);
    }
    
    $headers = str_getcsv($headerLine);
    
    expect($headers)->toBe([
        'File Name',
        'Owner',
        'Date Uploaded',
        'College',
        'Program',
        'Description/Tags'
    ]);
});

test('taskforce user can only see/access events of their own college and program', function () {
    $college1 = College::find(1);
    $college2 = College::find(2);
    $program1 = Program::find(1);
    $program2 = Program::find(2);

    $event1 = AccreditationEvent::factory()->create([
        'college_id' => $college1->id,
        'program_id' => $program1->id,
        'status' => 'active',
    ]);
    $event2 = AccreditationEvent::factory()->create([
        'college_id' => $college2->id,
        'program_id' => $program2->id,
        'status' => 'active',
    ]);

    $user = User::factory()->create([
        'college_id' => $college1->id,
        'program_id' => $program1->id,
    ]);
    $user->assignRole('taskforce');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    // 1. Visit events index
    $response = $this->get(route('events.index'));
    $response->assertStatus(200);
    $events = $response->viewData('page')['props']['events']['data'];
    
    // Should see event1 but not event2
    $eventIds = collect($events)->pluck('id');
    expect($eventIds)->toContain($event1->id);
    expect($eventIds)->not->toContain($event2->id);

    // 2. Direct access to event1 areas (allowed)
    $response = $this->get(route('areas', $event1));
    $response->assertStatus(200);

    // 3. Direct access to event2 areas (blocked with 403)
    $response = $this->get(route('areas', $event2));
    $response->assertStatus(403);
});

test('college officer user can only see/access events of their own college', function () {
    $college1 = College::find(1);
    $college2 = College::find(2);
    $program1 = Program::find(1);
    $program2 = Program::find(2);

    $event1 = AccreditationEvent::factory()->create([
        'college_id' => $college1->id,
        'program_id' => $program1->id,
        'status' => 'active',
    ]);
    $event2 = AccreditationEvent::factory()->create([
        'college_id' => $college2->id,
        'program_id' => $program2->id,
        'status' => 'active',
    ]);

    $user = User::factory()->create([
        'college_id' => $college1->id,
        'program_id' => null,
    ]);
    $user->assignRole('college_officer');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    // 1. Visit events index
    $response = $this->get(route('events.index'));
    $response->assertStatus(200);
    $events = $response->viewData('page')['props']['events']['data'];
    
    // Should see event1 but not event2
    $eventIds = collect($events)->pluck('id');
    expect($eventIds)->toContain($event1->id);
    expect($eventIds)->not->toContain($event2->id);

    // 2. Direct access to event1 areas (allowed)
    $response = $this->get(route('areas', $event1));
    $response->assertStatus(200);

    // 3. Direct access to event2 areas (blocked with 403)
    $response = $this->get(route('areas', $event2));
    $response->assertStatus(403);
});

test('college officer uploading general file succeeds with is_general = true and program_id = null', function () {
    $college = College::create(['code' => 'COT', 'name' => 'College of Technology']);

    $user = User::factory()->create([
        'college_id' => $college->id,
    ]);
    $user->assignRole('college_officer');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    $response = $this->postJson(route('api.files.temp'), [
        'filename' => 'general_doc.pdf',
        'metadata' => [
            'title' => 'General Doc',
            'is_general' => true,
        ],
    ]);

    $response->assertStatus(200);
    $tmpId = $response->json('tmp_id');
    
    $file = File::where('tmp_id', $tmpId)->first();
    expect($file->is_general)->toBe(true);
    expect($file->program_id)->toBeNull();
});

test('college officer uploading specific program file requires program_id and saves it', function () {
    $college = College::create(['code' => 'COE', 'name' => 'College of Engineering']);
    $program = Program::create([
        'code' => 'BSCE',
        'name' => 'Civil Engineering',
        'college_id' => $college->id
    ]);

    $user = User::factory()->create([
        'college_id' => $college->id,
    ]);
    $user->assignRole('college_officer');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    $response = $this->postJson(route('api.files.temp'), [
        'filename' => 'program_doc.pdf',
        'metadata' => [
            'title' => 'Program Doc',
            'is_general' => false,
            'program_id' => $program->id,
        ],
    ]);

    $response->assertStatus(200);
    $tmpId = $response->json('tmp_id');
    
    $file = File::where('tmp_id', $tmpId)->first();
    expect($file->is_general)->toBe(false);
    expect($file->program_id)->toBe($program->id);
});

test('college officer uploading specific program file without program_id fails validation', function () {
    $college = College::create(['code' => 'COA', 'name' => 'College of Arts']);

    $user = User::factory()->create([
        'college_id' => $college->id,
    ]);
    $user->assignRole('college_officer');

    $this->actingAs($user);
    $this->withoutMiddleware(CheckRoleStatus::class);

    $response = $this->postJson(route('api.files.temp'), [
        'filename' => 'program_doc.pdf',
        'metadata' => [
            'title' => 'Program Doc',
            'is_general' => false,
        ],
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['metadata.program_id']);
});

test('only the owner of a general file can delete it', function () {
    $college = College::create(['code' => 'COB', 'name' => 'College of Business']);

    $owner = User::factory()->create([
        'college_id' => $college->id,
    ]);
    $owner->assignRole('college_officer');

    $otherUser = User::factory()->create([
        'college_id' => $college->id,
    ]);
    $otherUser->assignRole('college_officer');

    $file = File::factory()->create([
        'uploaded_by' => $owner->id,
        'college_id' => $college->id,
        'is_general' => true,
    ]);

    // 1. Try deleting as other college officer (should be blocked)
    $this->actingAs($otherUser);
    $this->withoutMiddleware(CheckRoleStatus::class);
    $response = $this->delete(route('files.destroy', $file));
    $response->assertStatus(403);

    // 2. Try deleting as owner (should succeed)
    $this->actingAs($owner);
    $response = $this->delete(route('files.destroy', $file));
    $response->assertStatus(302); // Redirect back
});

test('non-owner admin cannot delete a general file', function () {
    $college = College::create(['code' => 'COM', 'name' => 'College of Music']);

    $owner = User::factory()->create([
        'college_id' => $college->id,
    ]);
    $owner->assignRole('college_officer');

    $admin = User::factory()->create([
        'college_id' => $college->id,
    ]);
    $admin->assignRole('admin');

    $file = File::factory()->create([
        'uploaded_by' => $owner->id,
        'college_id' => $college->id,
        'is_general' => true,
    ]);

    // Try deleting as admin (should be blocked since general files can ONLY be deleted by their uploader/owner)
    $this->actingAs($admin);
    $this->withoutMiddleware(CheckRoleStatus::class);
    $response = $this->delete(route('files.destroy', $file));
    $response->assertStatus(403);
});
