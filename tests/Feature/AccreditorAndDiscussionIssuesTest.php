<?php

use App\Models\Area;
use App\Models\File;
use App\Models\User;
use App\Models\College;
use App\Models\Comment;
use App\Models\Program;
use App\Models\Accreditor;
use App\Models\AccreditationEvent;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CommentReplyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'taskforce', 'guard_name' => 'web']);
    Role::create(['name' => 'ido_staff', 'guard_name' => 'web']);
    Role::create(['name' => 'college_officer', 'guard_name' => 'web']);

    College::create(['id' => 1, 'code' => 'CAS', 'name' => 'College of Arts and Sciences']);
    Program::create(['id' => 1, 'code' => 'BSCS', 'name' => 'BS Computer Science', 'college_id' => 1]);
    Area::create(['id' => 1, 'code' => 'A1', 'slug' => 'area-1', 'name' => 'Area I', 'order_no' => 1]);
});

test('activity logs page loads without error when an active accreditor has logged activity', function () {
    $admin = User::factory()->create([
        'college_id' => 1,
        'is_active' => true,
        'role_status' => 'approved',
    ]);
    $admin->assignRole('admin');

    $accreditor = Accreditor::factory()->create([
        'name' => 'Active Accreditor',
        'email' => 'accreditor@example.com',
        'college_id' => 1,
        'program_id' => 1,
        'created_by' => $admin->id,
        'is_active' => true,
        'role_status' => 'approved',
        'expires_at' => now()->addDays(7),
    ]);

    activity()
        ->useLog('authentication')
        ->performedOn($accreditor)
        ->causedBy($accreditor)
        ->log('Accreditor logged in');

    activity()
        ->useLog('authentication')
        ->performedOn($admin)
        ->causedBy($admin)
        ->log('Admin logged in');

    $this->actingAs($admin, 'web');

    $response = $this->get(route('activity-logs'));
    $response->assertStatus(200);

    $logs = $response->viewData('page')['props']['logs']['data'];
    expect($logs)->toHaveCount(2);

    // Filter by accreditor role
    $filteredResponse = $this->get(route('activity-logs', ['role' => 'accreditor']));
    $filteredResponse->assertStatus(200);
    $filteredLogs = json_decode(json_encode($filteredResponse->viewData('page')['props']['logs']['data']), true);
    expect($filteredLogs)->toHaveCount(1);
    expect($filteredLogs[0]['causer']['email'])->toBe('accreditor@example.com');
    expect($filteredLogs[0]['causer']['roles'][0]['name'])->toBe('accreditor');
});

test('replying to a comment in discussion sends CommentReplyNotification to thread participants', function () {
    Notification::fake();

    $taskforce = User::factory()->create([
        'college_id' => 1,
        'program_id' => 1,
        'is_active' => true,
        'role_status' => 'approved',
    ]);
    $taskforce->assignRole('taskforce');

    $accreditor = Accreditor::factory()->create([
        'name' => 'Dr. Accreditor',
        'email' => 'dr.accreditor@example.com',
        'college_id' => 1,
        'program_id' => 1,
        'created_by' => $taskforce->id,
        'is_active' => true,
        'role_status' => 'approved',
        'expires_at' => now()->addDays(7),
    ]);

    $event = AccreditationEvent::factory()->create([
        'college_id' => 1,
        'program_id' => 1,
        'status' => 'active',
        'expires_at' => now()->addDays(7),
    ]);
    $accreditor->events()->attach($event->id);
    $area = Area::find(1);

    // Accreditor posts a top-level comment
    $parentComment = Comment::create([
        'commentable_id' => $accreditor->id,
        'commentable_type' => Accreditor::class,
        'accreditation_event_id' => $event->id,
        'area_id' => $area->id,
        'parent_id' => null,
        'content' => 'Please clarify the faculty profile document.',
    ]);

    // Taskforce member replies to the accreditor's comment
    $this->actingAs($taskforce, 'web');
    $response = $this->postJson(route('comments.store', ['event' => $event->id, 'area' => $area->id]), [
        'content' => 'We have uploaded the updated faculty profile.',
        'parent_id' => $parentComment->id,
    ]);

    $response->assertStatus(200);

    Notification::assertSentTo(
        $accreditor,
        CommentReplyNotification::class,
        function (CommentReplyNotification $notification) use ($accreditor) {
            $mail = $notification->toMail($accreditor);
            return str_contains($mail->subject, 'New Reply in Discussion') &&
                   in_array('mail', $notification->via($accreditor));
        }
    );
    Notification::assertNotSentTo($taskforce, CommentReplyNotification::class);
});

test('accreditor can open shared permanent link and view or download assigned event file', function () {
    Storage::fake('local');

    $uploader = User::factory()->create([
        'college_id' => 1,
        'program_id' => 1,
        'is_active' => true,
        'role_status' => 'approved',
    ]);
    $uploader->assignRole('taskforce');

    $accreditor = Accreditor::factory()->create([
        'name' => 'Reviewer One',
        'email' => 'reviewer1@example.com',
        'college_id' => null,
        'program_id' => null,
        'created_by' => $uploader->id,
        'is_active' => true,
        'role_status' => 'approved',
        'expires_at' => now()->addDays(7),
    ]);

    $event = AccreditationEvent::factory()->create([
        'college_id' => 1,
        'program_id' => 1,
        'status' => 'active',
        'expires_at' => now()->addDays(7),
    ]);
    $accreditor->events()->attach($event->id);

    Storage::disk('local')->put('uploads/sample.pdf', '%PDF-1.4 sample content');

    $file = File::factory()->create([
        'title' => 'Curriculum Matrix',
        'original_filename' => 'curriculum_matrix.pdf',
        'extension' => 'pdf',
        'path' => 'private/uploads/sample.pdf',
        'college_id' => 1,
        'program_id' => 1,
        'uploaded_by' => $uploader->id,
        'is_general' => false,
    ]);

    $event->files()->attach($file->id, ['area_id' => 1, 'shared_by' => $uploader->id]);

    $this->actingAs($accreditor, 'accreditor');

    // 1. Open permanent shared link (/accreditation/files/p/{uuid})
    $sharedResponse = $this->get(route('files.shared', $file->uuid));
    $sharedResponse->assertStatus(200);
    expect($sharedResponse->viewData('page')['props']['isAccessible'])->toBeTrue();

    // 2. Inline view (/files/view/{file})
    $viewResponse = $this->get(route('files.view', $file->id));
    $viewResponse->assertStatus(200);

    // 3. Download (/files/download/{file})
    $downloadResponse = $this->get(route('files.download', $file->id));
    $downloadResponse->assertStatus(200);
});

