<?php

use App\Models\File;
use App\Models\User;
use App\Models\Accreditor;
use App\Models\AccreditationEvent;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'taskforce', 'guard_name' => 'web']);
    Role::create(['name' => 'ido_staff', 'guard_name' => 'web']);
    Role::create(['name' => 'college_officer', 'guard_name' => 'web']);
});

test('admin can see all files', function () {
    $admin = User::factory()->create();
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
        'accreditation_event_id' => $event1->id
    ]);

    $file1 = File::factory()->create();
    $file2 = File::factory()->create();

    // Share file1 to event1, area 1
    $event1->files()->attach($file1->id, ['area_id' => 1]);
    // file2 is not shared or shared to event2
    $event2->files()->attach($file2->id, ['area_id' => 1]);

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
    $otherPersonalFile = File::factory()->create(['is_general' => false]);

    $this->actingAs($user);
    $accessibleFiles = File::accessibleBy($user)->get();

    expect($accessibleFiles)->toHaveCount(2);
    expect($accessibleFiles->pluck('id'))->toContain($fileInCollege->id, $myPersonalFile->id);
    expect($accessibleFiles->pluck('id'))->not->toContain($fileInOtherCollege->id, $otherPersonalFile->id);
});
