<?php

namespace App\Http\Controllers\Owner\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Event\CreateEventRequest;
use App\Http\Requests\Owner\Event\UpdateEventRequest;
use App\Http\Resources\Owner\Event\EventResource;
use App\Models\Event\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display a listing of events
     *
     * Retrieve a paginated list of events for the authenticated owner.
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @group Owner Events
     * @queryParam status string nullable Filter by status: upcoming, live, passed. Example: upcoming
     *
     * @response 200 {
     *  "success": true,
     *  "message": "Events retrieved successfully.",
     *  "data": [{"id":1, "title":"Summer Gala"}],
     *  "meta": {"current_page":1, "last_page":1}
     * }
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $status = $request->get('status');

        $events = Event::with(['owner', 'poster'])
            ->where('owner_id', $user->id)
            ->when($status, function ($query, $status) {
                return match ($status) {
                    'upcoming' => $query->where('start_date', '>', now()),
                    'live' => $query->where('start_date', '<=', now())
                        ->where('end_date', '>=', now()),
                    'passed' => $query->where('end_date', '<', now()),
                    default => $query
                };
            })
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return $this->paginatedResponse(
            $events,
            EventResource::class,
            'Events retrieved successfully.'
        );
    }

    /**
     * Store a newly created event
     *
     * Create a new event for the authenticated owner.
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @group Owner Events
     *
     * @bodyParam title string required Event title. Example: Summer Gala
     * @bodyParam location string required Event location enum. Example: venue
     * @bodyParam location_description string required Location details. Example: "Main hall"
     * @bodyParam description string required Event description.
     * @bodyParam start_date string required Start date time format: Y-m-d H:i:s
     * @bodyParam end_date string required End date time format: Y-m-d H:i:s
     * @bodyParam max_attendees integer nullable Maximum number of attendees.
     * @bodyParam poster file required Event poster image (jpeg,png,jpg,gif).
     *
     * @response 201 {
     *  "success": true,
     *  "message": "Event created successfully.",
     *  "data": {"id": 1, "title": "Summer Gala"}
     * }
     */

    /**
     * Store a newly created event
     */
    public function store(CreateEventRequest $request)
    {
        $user = $request->user();

        return DB::transaction(function () use ($request, $user) {
            $validated = $request->validated();
            $validated['owner_id'] = $user->id;
            $event = Event::create($validated);

            if ($request->hasFile('poster')) {
                $event->uploadMedia($request->file('poster'), 'poster');
            }

            $event->load(['owner', 'poster']);

            return $this->sendResponse(
                new EventResource($event),
                'Event created successfully.',
                [],
                201
            );
        });
    }

    /**
     * Display the specified event (Organizer only)
     *
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @group Owner Events
     * @urlParam id integer required The ID of the event. Example: 1
     *
     * @response 200 {
     *  "success": true,
     *  "message": "Event retrieved successfully.",
     *  "data": {"id":1, "title":"Summer Gala"}
     * }
     * @response 404 {
     *  "success": false,
     *  "message": "Event not found or access denied."
     * }
     */
    public function show($id, Request $request)
    {
        $user = $request->user();

        $event = Event::with(['owner', 'poster'])
            ->where('id', $id)
            ->where('owner_id', $user->id)
            ->first();

        if (!$event) {
            return $this->notFound('Event not found or access denied.');
        }

        return $this->sendResponse(
            new EventResource($event),
            'Event retrieved successfully.'
        );
    }

    /**
     * Update the specified event (Organizer only)
     *
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @group Owner Events
     * @urlParam id integer required The ID of the event to update. Example: 1
     *
     * @bodyParam title string nullable Event title. Example: Summer Gala
     * @bodyParam location string nullable Location enum. Example: venue
     * @bodyParam location_description string nullable Location details. Example: "Main hall"
     * @bodyParam description string nullable Event description.
     * @bodyParam start_date string nullable Start date time format: Y-m-d H:i:s
     * @bodyParam end_date string nullable End date time format: Y-m-d H:i:s
     * @bodyParam max_attendees integer nullable Maximum number of attendees.
     * @bodyParam poster file nullable Event poster image (jpeg,png,jpg,gif).
     *
     * @response 200 {
     *  "success": true,
     *  "message": "Event updated successfully.",
     *  "data": {"id":1, "title":"Updated"}
     * }
     * @response 404 {
     *  "success": false,
     *  "message": "Event not found or access denied."
     * }
     */
    public function update(UpdateEventRequest $request, $id)
    {
        $user = $request->user();

        $event = Event::where('id', $id)
            ->where('owner_id', $user->id)
            ->first();

        if (!$event) {
            return $this->notFound('Event not found or access denied.');
        }

        return DB::transaction(function () use ($request, $event) {
            $validated = $request->validated();
            $event->update($validated);

            if ($request->hasFile('poster')) {
                $event->clearMediaCollection('poster');
                $event->uploadMedia($request->file('poster'), 'poster');
            }

            $event->fresh()->load(['owner', 'poster']);

            return $this->sendResponse(
                new EventResource($event),
                'Event updated successfully.'
            );
        });
    }

    /**
     * Remove the specified event (Organizer only)
     *
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @group Owner Events
     * @urlParam id integer required The ID of the event to delete. Example: 1
     *
     * @response 200 {"success": true, "message": "Event deleted successfully."}
     * @response 404 {"success": false, "message": "Event not found or access denied."}
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();

        $event = Event::where('id', $id)
            ->where('owner_id', $user->id)
            ->first();

        if (!$event) {
            return $this->notFound('Event not found or access denied.');
        }

        $event->clearMediaCollection('poster');
        $event->delete();

        return $this->sendResponse(null, 'Event deleted successfully.');
    }
}
