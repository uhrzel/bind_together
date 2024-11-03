@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Report Generation Admin Sport</h4>
        </div>
        <div class="card-body" style="min-height: 70vh">
            <form action="{{ route('reports.generateAdminSport') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-4">
                        <label for="type">Type of Report</label>
                        <select name="type" id="type_of_report" class="form-select">
                            <option value="" selected disabled>Select Type of Report</option>
                            <option value="3">Activity</option>
                            <option value="1">List of Registered Participants</option>
                            <option value="2">List of Official Players</option>
                            <option value="4">List of Coaches</option>
                        </select>

                        <div id="activitiesDropdown" style="display: none;" class="mt-3">
                            <label for="activity_type">Activity Type</label>
                            <select name="activity_type" id="activity_type" class="form-select">
                                <option value="" selected disabled>Select Activity Type</option>
                                <option value="3">Competition</option>
                                <option value="1">Tryouts</option>
                            </select>
                        </div>
                        <div class="form-group col-4 mt-5" id="approvalCheckboxes" style="display: none;">
                            <label>Status</label><br>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="status[]" value="approve" id="approve">
                                <label for="approve" class="form-check-label">Approve</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="status[]" value="declined" id="declined">
                                <label for="declined" class="form-check-label">Decline</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="status[]" value="all" id="all">
                                <label for="all" class="form-check-label">All</label>
                            </div>
                        </div>

                    </div>


                    <div class="form-group col-2">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>

                    <div class="form-group col-2">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" class="form-control">

                        <div class="form-group mt-3" id="yearLevelSection">
                            <label for="">Year Level</label><br>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="year_level[]" value="1" id="first_year">
                                <label for="first_year" class="form-check-label">1st Year</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="year_level[]" value="2" id="second_year">
                                <label for="second_year" class="form-check-label">2nd Year</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="year_level[]" value="3" id="third_year">
                                <label for="third_year" class="form-check-label">3rd Year</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="year_level[]" value="4" id="fourth_year">
                                <label for="fourth_year" class="form-check-label">4th Year</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="year_level[]" value="5" id="fifth_year">
                                <label for="fifth_year" class="form-check-label">5th Year</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-4" id="sportsCheckbox">
                        <label for="sports">Sports Name</label><br>

                        <!-- "All" checkbox -->
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="sports_all" id="all_sports">
                            <label for="all_sports" class="form-check-label">All</label>
                        </div>

                        <!-- Sports checkboxes -->
                        @foreach ($sports as $sport)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input sport-checkbox" name="sports[]" value="{{ $sport->id }}" id="sport_{{ $sport->id }}">
                            <label for="sport_{{ $sport->id }}" class="form-check-label">{{ $sport->name }}</label>
                        </div>
                        @endforeach

                        <!-- "Others" checkbox -->
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="sports[]" value="others" id="sports_others">
                            <label for="sports_others" class="form-check-label">Others</label>
                        </div>
                    </div>

                </div>


                <div class="mt-5 d-flex">
                    <button type="submit" class="btn btn-primary">Download</button>
                    <select name="file_type" id="file_type" class="form-select w-25">
                        <option value="" disabled selected>Select File Type</option>
                        <option value="docx">Docx</option>
                        <option value="pdf">Pdf</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toggle visibility of activity type dropdown based on selected report type
        $('#type_of_report').on('change', function() {
            const selectedValue = $(this).val();
            if (selectedValue == 3) {
                $('#activitiesDropdown').slideDown();
            } else {
                $('#activitiesDropdown').slideUp();
            }
            if (selectedValue == 1) {
                $('#approvalCheckboxes').slideDown();
            } else {
                $('#approvalCheckboxes').slideUp();
            }
            if (selectedValue == 3 || selectedValue == 4) {
                $('#yearLevelSection').slideUp();
            } else {
                $('#yearLevelSection').slideDown();
            }
            /* if (selectedValue == 3) {
                $('#sportsCheckbox').slideUp();
            } else {
                $('#sportsCheckbox').slideDown();
            } */
        });


        // Check or uncheck all sports when "All" is clicked
        $('#all_sports').on('change', function() {
            $('.sport-checkbox').prop('checked', $(this).is(':checked'));
        });

        // Uncheck "All" if any individual sport is unchecked
        $('.sport-checkbox').on('change', function() {
            if (!$(this).is(':checked')) {
                $('#all_sports').prop('checked', false);
            }
        });
    });
</script>
@endsection