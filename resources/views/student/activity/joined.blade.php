@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Joined Activities</h4>
            </div>
            <div class="card-body">
                <div class="container my-4">
                    <!-- Search Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="search" class="form-label fw-bold">Search</label>
                            <input type="text" id="search" class="form-control" placeholder="Search For Activities...">
                        </div>
                    </div>

                    <!-- Activities Section -->
                    <div class="row">
                        @foreach ($joinedActivities as $activity)
                            @php
                                $activityTypes = [
                                    \App\Enums\ActivityType::Audition => 'Audition',
                                    \App\Enums\ActivityType::Tryout => 'Tryout',
                                    \App\Enums\ActivityType::Practice => 'Practice',
                                    \App\Enums\ActivityType::Competition => 'Competition',
                                ];

                                $hasJoined = auth()
                                    ->user()
                                    ->joinedActivities->contains($activity->id);
                            @endphp

                            <div class="col-md-4 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $activity->title }}</h5>
                                        <p class="card-text">
                                            <strong>Sport name:</strong> {{ $activity->sport->name ?? '' }} <br>
                                            <strong>Type:</strong> {{ $activityTypes[$activity->type] ?? '' }} <br>
                                            <strong>Venue:</strong> {{ $activity->venue }} <br>
                                            <strong>Duration:</strong> {{ $activity->start_date }} -
                                            {{ $activity->end_date }}
                                        </p>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between">
                                        <button class="btn btn-dark view-button" data-activity-id="{{ $activity->id }}"
                                            data-bs-toggle="modal" data-bs-target="#activityDetailsModal">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
