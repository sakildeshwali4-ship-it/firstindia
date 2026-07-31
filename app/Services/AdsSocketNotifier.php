<?php

namespace App\Services;

use App\Models\Ads;
use App\Models\AdAssignment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdsSocketNotifier
{
    public function notifyTargets($targets, $reason = 'admin_updated_ad')
    {
        $collection = collect($targets)
            ->filter(function ($target) {
                return !empty($target['type']) && !empty($target['id']);
            })
            ->unique(function ($target) {
                return $target['type'] . ':' . $target['id'];
            })
            ->values();

        foreach ($collection as $target) {
            if ($target['type'] === 'video') {
                $this->publishVodRoom((int) $target['id'], $reason);
                continue;
            }

            if ($target['type'] === 'live_tv') {
                $this->publishLiveTvRoom((int) $target['id'], $reason);
            }
        }
    }

    public function notifyAdAssignments($adId, $reason = 'admin_updated_ad')
    {
        $targets = AdAssignment::where('ad_id', $adId)
            ->get(['assignable_type', 'assignable_id'])
            ->map(function ($assignment) {
                return [
                    'type' => $assignment->assignable_type,
                    'id' => (int) $assignment->assignable_id,
                ];
            });

        $this->notifyTargets($targets, $reason);
    }

    public function publishVodRoom($videoId, $reason = 'admin_updated_ad')
    {
        $payload = $this->buildVodPayload($videoId, $reason);

        return $this->publishToRoom(
            'ads:vod:' . $videoId,
            'ads:updated',
            $payload
        );
    }

    public function publishLiveTvRoom($channelId, $reason = 'admin_updated_ad')
    {
        $payload = $this->buildLiveTvPayload($channelId, $reason);

        return $this->publishToRoom(
            'ads:live_tv:' . $channelId,
            'ads:updated',
            $payload
        );
    }

    public function buildVodPayload($videoId, $reason = 'admin_updated_ad')
    {
        $rows = DB::table('ads as a')
            ->join('ad_assignments as aa', 'aa.ad_id', '=', 'a.id')
            ->where('aa.assignable_type', 'video')
            ->where('aa.assignable_id', $videoId)
            ->where('aa.active', 1)
            ->where('a.active', 1)
            ->select(
                'a.id',
                'a.type',
                'a.title',
                'a.media_url',
                'a.media_type',
                'a.click_url',
                'a.start_after_seconds',
                'a.repeat_every_seconds',
                'a.duration_seconds',
                'a.skippable_after_seconds',
                'a.priority',
                'a.active',
                'a.updated_at as ad_updated_at',
                'aa.updated_at as assignment_updated_at',
                'aa.ad_position'
            )
            ->orderBy('a.priority')
            ->get();

        return [
            'content_type' => 'vod',
            'video_id' => (int) $videoId,
            'ads_version' => $this->resolveVersion($rows),
            'change_reason' => $reason,
            'server_time' => now()->toIso8601String(),
            'ads' => $this->mapVodAds($rows),
        ];
    }

    public function buildLiveTvPayload($channelId, $reason = 'admin_updated_ad')
    {
        $rows = DB::table('ads as a')
            ->join('ad_assignments as aa', 'aa.ad_id', '=', 'a.id')
            ->where('aa.assignable_type', 'live_tv')
            ->where('aa.assignable_id', $channelId)
            ->where('aa.active', 1)
            ->where('a.active', 1)
            ->select(
                'a.id',
                'a.type',
                'a.title',
                'a.media_url',
                'a.media_type',
                'a.click_url',
                'a.start_after_seconds',
                'a.repeat_every_seconds',
                'a.duration_seconds',
                'a.skippable_after_seconds',
                'a.priority',
                'a.active',
                'a.updated_at as ad_updated_at',
                'aa.updated_at as assignment_updated_at',
                'aa.ad_position'
            )
            ->orderBy('a.priority')
            ->get();

        return [
            'content_type' => 'live_tv',
            'channel_id' => (int) $channelId,
            'ads_version' => $this->resolveVersion($rows),
            'change_reason' => $reason,
            'server_time' => now()->toIso8601String(),
            'ads' => $this->mapLiveTvAds($rows, $channelId),
        ];
    }

    private function mapVodAds(Collection $rows)
    {
        return $rows->map(function ($ad) {
            $startAfterSeconds = Ads::normalizeStartAfterSeconds($ad->start_after_seconds);

            return [
                'id' => (int) $ad->id,
                'content_type' => 'vod',
                'type' => $ad->type,
                'title' => $ad->title,
                'media_url' => $this->resolveMediaUrl($ad->media_type, $ad->media_url),
                'media_type' => $ad->media_type,
                'click_url' => $ad->click_url,
                'schedule_mode' => 'content_time',
                'cue_points_seconds' => $startAfterSeconds,
                'start_after_seconds' => (int) ($startAfterSeconds[0] ?? 0),
                'repeat_every_seconds' => (int) $ad->repeat_every_seconds,
                'duration_seconds' => (int) $ad->duration_seconds,
                'skippable_after_seconds' => (int) $ad->skippable_after_seconds,
                'position' => $ad->ad_position,
                'priority' => (int) $ad->priority,
                'active' => (bool) $ad->active,
                'updated_at' => $this->toIso8601($ad->ad_updated_at),
            ];
        })->values()->all();
    }

    private function mapLiveTvAds(Collection $rows, $channelId)
    {
        return $rows->map(function ($ad) use ($channelId) {
            $startAfterSeconds = Ads::normalizeStartAfterSeconds($ad->start_after_seconds);

            return [
                'id' => (int) $ad->id,
                'content_type' => 'live_tv',
                'channel_ids' => [(int) $channelId],
                'type' => $ad->type,
                'title' => $ad->title,
                'media_url' => $this->resolveMediaUrl($ad->media_type, $ad->media_url),
                'media_type' => $ad->media_type,
                'click_url' => $ad->click_url,
                'schedule_mode' => 'watch_elapsed',
                'cue_points_seconds' => [],
                'start_after_seconds' => (int) ($startAfterSeconds[0] ?? 0),
                'repeat_every_seconds' => (int) $ad->repeat_every_seconds,
                'duration_seconds' => (int) $ad->duration_seconds,
                'skippable_after_seconds' => (int) $ad->skippable_after_seconds,
                'position' => $ad->ad_position,
                'priority' => (int) $ad->priority,
                'active' => (bool) $ad->active,
                'updated_at' => $this->toIso8601($ad->ad_updated_at),
            ];
        })->values()->all();
    }

    private function publishToRoom($room, $event, array $payload)
    {
        if (!config('ads_socket.enabled')) {
            return false;
        }

        try {
            $response = Http::timeout((int) config('ads_socket.request_timeout', 3))
                ->withHeaders([
                    'X-Ads-Socket-Token' => (string) config('ads_socket.publish_token'),
                ])
                ->post(rtrim(config('ads_socket.server_url'), '/') . '/internal/publish', [
                    'room' => $room,
                    'event' => $event,
                    'payload' => $payload,
                    'cache' => true,
                ]);

            if (!$response->successful()) {
                Log::warning('Ads socket publish failed.', [
                    'room' => $room,
                    'event' => $event,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return $response->successful();
        } catch (Throwable $throwable) {
            Log::warning('Ads socket publish exception.', [
                'room' => $room,
                'event' => $event,
                'message' => $throwable->getMessage(),
            ]);

            return false;
        }
    }

    private function resolveVersion(Collection $rows)
    {
        $latestTimestamp = $rows->reduce(function ($carry, $row) {
            $adUpdatedAt = $row->ad_updated_at ? strtotime($row->ad_updated_at) : 0;
            $assignmentUpdatedAt = $row->assignment_updated_at ? strtotime($row->assignment_updated_at) : 0;
            $rowLatest = max($adUpdatedAt, $assignmentUpdatedAt);

            return max((int) $carry, (int) $rowLatest);
        }, 0);

        return $latestTimestamp ?: now()->timestamp;
    }

    private function resolveMediaUrl($mediaType, $mediaUrl)
    {
        if ($mediaType === 'image' && !empty($mediaUrl)) {
            return asset($mediaUrl);
        }

        return $mediaUrl;
    }

    private function toIso8601($value)
    {
        if (empty($value)) {
            return now()->toIso8601String();
        }

        return Carbon::parse($value)->toIso8601String();
    }
}
