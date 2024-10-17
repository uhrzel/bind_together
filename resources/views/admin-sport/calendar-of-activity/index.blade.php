@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Calendar of Activity</h4>
            </div>
            <div class="card-body">
                <div class="" id="calendar"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
        const calendarOfActivity = $('#calendar')[0];

        const calendar = new FullCalendar.Calendar(calendarOfActivity, {
            initialView: 'dayGridMonth',
            events: {!! json_encode($activities) !!},
        });

        calendar.render();
    </script>
@endpush
