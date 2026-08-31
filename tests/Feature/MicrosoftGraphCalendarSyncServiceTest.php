<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Services\Graph\MicrosoftGraphCalendarSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MicrosoftGraphCalendarSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.microsoft_graph.tenant_id' => 'test-tenant',
            'services.microsoft_graph.client_id' => 'test-client',
            'services.microsoft_graph.client_secret' => 'test-secret',
            'services.microsoft_graph.calendar_user_id' => 'dispatcher@fleetdesk.test',
        ]);
    }

    public function test_it_creates_a_calendar_event_and_stores_the_graph_event_id(): void
    {
        Http::fake([
            'https://login.microsoftonline.com/*' => Http::response(['access_token' => 'fake-token'], 200),
            'https://graph.microsoft.com/v1.0/users/*/events' => Http::response(['id' => 'graph-event-123'], 201),
        ]);

        $job = Job::factory()->assigned()->create();

        (new MicrosoftGraphCalendarSyncService)->sync($job);

        Http::assertSent(fn ($request) => $request->url() === 'https://login.microsoftonline.com/test-tenant/oauth2/v2.0/token'
            && $request['client_id'] === 'test-client'
            && $request['grant_type'] === 'client_credentials');

        Http::assertSent(fn ($request) => $request->url() === 'https://graph.microsoft.com/v1.0/users/dispatcher@fleetdesk.test/events'
            && $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Bearer fake-token'));

        $this->assertSame('graph-event-123', $job->fresh()->graph_event_id);
    }

    public function test_it_updates_the_existing_event_when_a_graph_event_id_is_already_stored(): void
    {
        Http::fake([
            'https://login.microsoftonline.com/*' => Http::response(['access_token' => 'fake-token'], 200),
            'https://graph.microsoft.com/v1.0/users/*/events/*' => Http::response(['id' => 'graph-event-123'], 200),
        ]);

        $job = Job::factory()->completed()->create(['graph_event_id' => 'graph-event-123']);

        (new MicrosoftGraphCalendarSyncService)->sync($job);

        Http::assertSent(fn ($request) => $request->url() === 'https://graph.microsoft.com/v1.0/users/dispatcher@fleetdesk.test/events/graph-event-123'
            && $request->method() === 'PATCH');
    }

    public function test_it_does_nothing_when_graph_credentials_are_not_configured(): void
    {
        config(['services.microsoft_graph.tenant_id' => null]);

        Http::fake();

        $job = Job::factory()->assigned()->create();

        (new MicrosoftGraphCalendarSyncService)->sync($job);

        Http::assertNothingSent();
        $this->assertNull($job->fresh()->graph_event_id);
    }
}
