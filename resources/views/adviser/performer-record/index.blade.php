@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                @if ($status == 0)
                    <h4>Audition List</h4>
                @else
                    <h4>Official Player</h4>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Height</th>
                                <th>Weight</th>
                                <th>Type</th>
                                <th>Status</th>
                                @if ($status == 0)
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($auditions as $audition)
                                <tr>
                                    <td>{{ $audition->user->firstname }} {{ $audition->user->lastname }}</td>
                                    <td>{{ $audition->height }}</td>
                                    <td>{{ $audition->weight }}</td>
                                    <td>{{ $audition->type ?? '' }}</td>
                                    <td>{{ $audition->status }}</td>
                                    @if ($status == 0)
                                        <td>{{ $audition->status }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#datatable').DataTable();
    </script>
@endpush
