@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Report Generation</h4>
            </div>
            <div class="card-body" style="min-height: 70vh">
                <form action="{{ route('reports.generate') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-4">
                            <label for="">Type of Report</label>
                            <select name="type" id="" class="form-select">
                                <option value="" selected disabled>Select Type of Report</option>
                                <option value="3">Activity</option>
                                <option value="1">List of Registered Participants</option>
                                <option value="2">List of Official Performers</option>
                            </select>

                            <!-- Additional dropdown for activities -->
                            <div id="activitiesDropdown" style="display: none;">
                                <label for="">Activities</label>
                                <select name="activity_type" id="" class="form-select">
                                    <option value="competition">Competition</option>
                                    <option value="tryouts">Tryouts</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-2">
                            <label for="">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>

                        <div class="form-group col-2">
                            <label for="">End Date</label>
                            <input type="date" name="end_date" class="form-control">

                            <div class="form-group mt-3">
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

                        <div class="form-group col-4">
                            <label for="">Sports</label><br>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="sports_all" id="all_sports">
                                <label for="all_sports" class="form-check-label">All</label>
                            </div>
                            @foreach ($sports as $sport)
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input sport-checkbox" name="sports[]" value="{{ $sport->id }}" id="sport_{{ $sport->id }}">
                                    <label for="sport_{{ $sport->id }}" class="form-check-label">{{ $sport->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-5 d-flex">
                        <button type="submit" class="btn btn-primary">Download</button>
                        {{-- <button class="btn btn-success mx-3">Download</button> --}}
                        <select name="file_type" id="" class="form-select w-25">
                            <option value="" disabled selected>Select File Type</option>
                            <option value="docx">Docx</option>
                            <option value="pdf">Pdf</option>
                            {{-- <option value="excel">Excel</option> --}}
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('select[name="type"]').on('change', function() {
            if ($(this).val() == 3) {
                $('#activitiesDropdown').show();
            } else {
                $('#activitiesDropdown').hide();
            }
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
    </script>
@endsection
