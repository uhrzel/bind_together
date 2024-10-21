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

        // Map through activities and add necessary properties for FullCalendar events
        const events = {!! json_encode($activities) !!}.map(activity => {
            return {
                title: activity.title,
                start: activity.start, // Ensure 'start' is in 'YYYY-MM-DDTHH:mm:ss' format
                end: activity.end, // Ensure 'end' is in 'YYYY-MM-DDTHH:mm:ss' format
                backgroundColor: '#ff9f89', // Custom highlight color for the event
                borderColor: '#ff9f89', // Optional: you can style the border color as well
                textColor: '#000', // Optional: text color inside the event
                allDay: false // Set to true if it's an all-day event
            };
        });

        const calendar = new FullCalendar.Calendar(calendarOfActivity, {
            initialView: 'dayGridMonth',
            events: events, // Assign events to the calendar
        });

        calendar.render();
    </script>
@endpush
