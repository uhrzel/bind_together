@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Report Generation</h4>
            </div>
            <div class="card-body" style="min-height: 70vh">
                <form action="{{ route('report.generate') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-4">
                            <label for="">Type of Report</label>
                            <select name="type" id="" class="form-select">
                                <option value="" selected disabled>Select Type of Report</option>
                                <option value="4">Activities</option>
                                <option value="3">Practice</option>
                                <option value="1">List of Tryout Participants</option>
                                <option value="2">List of Official Performers</option>
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <label for="">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="form-group col-2">
                            <label for="">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-2 form-group">
                            <label for="">Status</label>
                            <select name="status" id="" class="form-select">
                                <option value="" selected disabled>Select Status</option>
                                <option value="1">Approve</option>
                                <option value="2">Decline</option>
                            </select>
                        </div>
                        <div class="col form-group">
                            <label for="">Year Level</label>
                            <select name="year_level" id="" class="form-select">
                                <option value="" selected disabled>Select Year Level</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-5 d-flex">
                        {{-- <button class="btn btn-primary mx-2">Print</button> --}}
                        <select name="file_type" id="" class="form-select w-25">
                            <option value="pdf">PDF</option>
                        </select>
                        <button type="submit" class="btn btn-primary mx-3">Download</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
