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

    // Check the format of the events in the console
    console.log({!! json_encode($activities) !!});

    // Map through activities and add necessary properties for FullCalendar events
    const events = {!! json_encode($activities) !!}.map(activity => {
        return {
            title: activity.title,
            start: activity.start_date, // Make sure 'start_date' is in 'YYYY-MM-DD' format
            end: activity.end_date,     // Make sure 'end_date' is in 'YYYY-MM-DD' format
            backgroundColor: '#ff9f89', // Custom highlight color for the event
            borderColor: '#ff9f89',     // Optional: you can style the border color as well
            textColor: '#000',          // Optional: text color inside the event
            allDay: true                // Assuming all events are full-day events
        };
    });

    const calendar = new FullCalendar.Calendar(calendarOfActivity, {
        initialView: 'dayGridMonth',
        events: events, // Assign events to the calendar
    });

    calendar.render();
</script>

@endpush
