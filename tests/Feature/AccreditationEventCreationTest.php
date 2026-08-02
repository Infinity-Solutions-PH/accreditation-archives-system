<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\College;
use App\Models\Program;
use Illuminate\Support\Carbon;
use App\Models\AccreditationEvent;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccreditationEventCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the roles if they don't exist
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'college_officer']);
        Role::firstOrCreate(['name' => 'taskforce']);
        Role::firstOrCreate(['name' => 'ido_staff']);
    }

    public function test_cannot_create_accreditation_event_with_same_details()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $college = College::create(['name' => 'Test College', 'code' => 'TC']);
        $program = Program::create(['name' => 'Test Program', 'code' => 'TP', 'college_id' => $college->id]);

        $eventData = [
            'title' => 'Level 1 Accreditation',
            'description' => 'Test description',
            'college_id' => $college->id,
            'program_id' => $program->id,
            'level' => 'Level 1',
            'expires_at' => Carbon::now()->addDays(30)->format('Y-m-d H:i:s'),
        ];

        // Create the first event
        $response1 = $this->actingAs($user)->post('/events', $eventData);
        $response1->assertSessionHasNoErrors();
        $this->assertDatabaseHas('accreditation_events', [
            'title' => 'Level 1 Accreditation',
            'college_id' => $college->id,
            'program_id' => $program->id,
            'level' => 'Level 1',
        ]);

        // Attempt to create the second event with the same details
        $response2 = $this->actingAs($user)->post('/events', $eventData);
        
        // Assert that the creation succeeds
        $response2->assertSessionHasNoErrors();
        $response2->assertRedirect();
        
        // Assert that two events exist in the database
        $this->assertDatabaseCount('accreditation_events', 2);

        // Retrieve them and verify slugs are different
        $events = AccreditationEvent::where('title', 'Level 1 Accreditation')->get();
        $this->assertCount(2, $events);
        $this->assertNotEquals($events[0]->slug, $events[1]->slug);
        $this->assertEquals('level-1-accreditation', $events[0]->slug);
        $this->assertEquals('level-1-accreditation-1', $events[1]->slug);
    }
}
