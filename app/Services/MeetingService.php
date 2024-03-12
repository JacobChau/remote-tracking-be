<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LinkAccessType;
use App\Http\Resources\MeetingScreenshotResource;
use App\Http\Resources\UserMeetingResource;
use App\Mail\MeetingInvited;
use App\Models\Meeting;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

class MeetingService extends BaseService
{
    protected LinkService $linkService;
    protected UserService $userService;
    protected LinkAccessService $linkAccessService;

    public function __construct(Meeting $meeting, LinkService $linkService, LinkAccessService $linkAccessService, UserService $userService)
    {
        $this->model = $meeting;
        $this->linkService = $linkService;
        $this->linkAccessService = $linkAccessService;
        $this->userService = $userService;
    }

    public function join(string $hash): JsonResponse
    {
        $linkSetting = $this->linkService->findOneOrFail(['hash' => $hash]);

        $meeting = $linkSetting->meeting;

        // create a record in the pivot table if the user is not already attached
        $meeting->users()->syncWithoutDetaching(auth()->user()->id);

        if (! $linkSetting->is_enabled) {
            return response()->json(['status' => 'link_disabled'], 403);
        }

        if ($linkSetting->end_date && $linkSetting->end_date < now()) {
            return response()->json(['status' => 'link_expired'], 403);
        }

        if ($linkSetting->start_date > now()) {
            return response()->json(['status' => 'link_not_started'], 403);
        }

        if ($linkSetting->access_type === LinkAccessType::PRIVATE) {
            $staffId = auth()->user()->id;
            $linkAccess = $this->linkAccessService->findOne(['link_id' => $linkSetting->id, 'user_id' => $staffId]);
            if (! $linkAccess) {
                return response()->json(['status' => 'access_denied'], 403);
            }
        }

        return response()->json(new UserMeetingResource($meeting));
    }

    public function getMeetingScreenshot(): array
    {
        $query = $this->model->query();

        return $this->getList(MeetingScreenshotResource::class, request()->all(), $query, ['screenshots']);
    }

    /**
     * @throws Exception
     */
    public function create(array $data): object
    {
        DB::beginTransaction();
        try {
            $meeting = parent::create([
                'title' => $data['title'],
                'start_date' => $data['startDate'] ?? null,
                'end_date' => $data['endDate'] ?? null,
            ]);

            $linkSetting = $meeting->linkSetting()->create([
                'access_type' => $data['accessType']->value,
                'is_enabled' => $data['linkEnabled'] ?? true,
                'start_date' => $data['startDate'] ?? null,
                'end_date' => $data['endDate'] ?? null,
                'hash' => substr(md5($meeting->id.microtime()), 0, 12),
            ]);

            if (isset($data['participants']) && is_array($data['participants'])) {
                foreach ($data['participants'] as $userId) {
                    $linkSetting->accesses()->create([
                        'user_id' => $userId,
                        'is_allowed' => true,
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $linkSetting;
    }

    public function showByHash(string $hash): object
    {
        // hash is store in the link setting, foreign key to the meeting
        $linkSetting = $this->linkService->findOneOrFail(['hash' => $hash]);

        return $linkSetting->meeting;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function getList(?string $resourceClass = null, array $input = [], ?Builder $query = null, array $relations = []): array
    {
        $query = $this->model->query();

        if (request()->has('filters')) {
            $filters = request()->get('filters');
            if (isset($filters['startDate'])) {
                $query->whereDate('start_date', '>=', $filters['startDate']);
            }

            if (isset($filters['endDate'])) {
                $query->whereDate('end_date', '<=', $filters['endDate']);
            }
        }

        if (request()->has('sort')) {
            $sort = explode(':', request()->get('sort'));
            $query->orderBy($sort[0], $sort[1]);
        }

        return parent::getList($resourceClass, $input, $query, $relations);
    }

    /**
     * @throws Exception
     */
    public function updateMeeting(Meeting $meeting, array $data): void
    {
        DB::beginTransaction();
        try {
            $linkSetting = $meeting->linkSetting()->firstOrFail();

            if (isset($data['participants']) && is_array($data['participants'])) {
                $currentParticipantIds = $linkSetting->accesses()->pluck('user_id')->toArray();
                $newParticipantIds = $data['participants'];

                // Compute the participants to add and to remove
                $participantIdsToAdd = array_diff($newParticipantIds, $currentParticipantIds);
                $participantIdsToRemove = array_diff($currentParticipantIds, $newParticipantIds);

                // Add new participants
                foreach ($participantIdsToAdd as $userId) {
                    $linkSetting->accesses()->create([
                        'user_id' => $userId,
                        'is_allowed' => true,
                    ]);
                }

                // Remove participants not in the new list
                if (! empty($participantIdsToRemove)) {
                    $linkSetting->accesses()->whereIn('user_id', $participantIdsToRemove)->delete();
                }

                $meeting->users()->sync($newParticipantIds);
            }

            if (isset($data['linkEnabled'])) {
                $linkSettingData['is_enabled'] = $data['linkEnabled'];
            }

            if (isset($data['accessType'])) {
                $linkSettingData['access_type'] = $data['accessType']->value;
            }

            if (isset($data['startDate'])) {
                $linkSettingData['start_date'] = $data['startDate'];
            }

            if (isset($data['endDate'])) {
                $linkSettingData['end_date'] = $data['endDate'];
            }

            if (isset($linkSettingData)) {
                $linkSetting->update($linkSettingData);
            }

            // Prepare meeting updates
            $meetingData = collect($data)->only(['title', 'startDate', 'endDate'])->filter()->all();
            $meeting->update($meetingData);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function invite(Meeting $meeting, array $emails): void
    {
        foreach ($emails as $email) {
            $linkSetting = $meeting->linkSetting()->first();
            $user = $this->userService->findOne(['email' => $email]);
            $meeting->users()->syncWithoutDetaching($user->id);
            $linkAccess = $this->linkAccessService->findOne(['link_id' => $linkSetting->id, 'user_id' => $user->id]);
            if (! $linkAccess) {
                $linkSetting->accesses()->create([
                    'user_id' => $user->id,
                    'is_allowed' => true,
                ]);
            }
        }
        Mail::to($emails)->send(new MeetingInvited($meeting));
    }
}
