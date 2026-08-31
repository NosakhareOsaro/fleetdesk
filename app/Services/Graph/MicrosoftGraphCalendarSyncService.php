<?php

namespace App\Services\Graph;

use App\Contracts\CalendarSyncService;
use App\Models\Job;
use Illuminate\Support\Facades\Http;

class MicrosoftGraphCalendarSyncService implements CalendarSyncService
{
    public function sync(Job $job): void
    {
        $token = $this->accessToken();
        $calendarUserId = config('services.microsoft_graph.calendar_user_id');
        $payload = $this->eventPayload($job);

        if ($job->graph_event_id) {
            Http::withToken($token)
                ->patch("https://graph.microsoft.com/v1.0/users/{$calendarUserId}/events/{$job->graph_event_id}", $payload)
                ->throw();

            return;
        }

        $response = Http::withToken($token)
            ->post("https://graph.microsoft.com/v1.0/users/{$calendarUserId}/events", $payload)
            ->throw();

        $job->forceFill(['graph_event_id' => $response->json('id')])->save();
    }

    /**
     * @return array<string, mixed>
     */
    private function eventPayload(Job $job): array
    {
        $start = $job->scheduled_at ?? now();

        return [
            'subject' => "Delivery: {$job->customer->name}",
            'body' => [
                'contentType' => 'Text',
                'content' => $job->notes ?? '',
            ],
            'start' => [
                'dateTime' => $start->toIso8601String(),
                'timeZone' => 'UTC',
            ],
            'end' => [
                'dateTime' => $start->copy()->addHour()->toIso8601String(),
                'timeZone' => 'UTC',
            ],
        ];
    }

    private function accessToken(): string
    {
        $tenantId = config('services.microsoft_graph.tenant_id');

        $response = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
            'client_id' => config('services.microsoft_graph.client_id'),
            'client_secret' => config('services.microsoft_graph.client_secret'),
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ])->throw();

        return $response->json('access_token');
    }
}
