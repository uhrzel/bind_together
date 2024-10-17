@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container my-4">
        <!-- Create Post Section -->
        <div class="card mb-4">
            <div class="card-body row">
                <div class="col-1">
                    <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="User Avatar">
                </div>
                <div class="col-11 mt-1">
                    <input type="text" style="cursor: pointer" class="form-control rounded-pill me-2"
                        data-bs-toggle="modal" data-bs-target="#newsfeedModal"
                        placeholder="What's on your mind, {{ auth()->user()->firstname }}?">
                </div>
                <div class="text-end mt-2">
                    <button class="btn btn-danger rounded-pill "><i class="fas fa-image"></i> Create Post</button>
                </div>
            </div>
        </div>

        @foreach ($newsfeeds as $newsfeed)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex">
                        <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="User Avatar">
                        <div>
                            <h6 class="mb-0">{{ $newsfeed->user->firstname }} {{ $newsfeed->user->lastname }}</h6>
                            <small>{{ $newsfeed->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <button class="btn btn-link text-muted"><i class="fas fa-ellipsis-h"></i></button>
                </div>
                <div class="card-body">
                    <p>{{ $newsfeed->description }}</p>

                    <div class="row">
                        @foreach ($newsfeed->newsfeedFiles as $file)
                            <div class="col-md-4 mb-3">
                                @if (Str::startsWith($file->file_type, 'image'))
                                    <img src="{{ asset('storage/' . $file->file_path) }}" class="img-fluid rounded"
                                        alt="Media Image">
                                @elseif (Str::startsWith($file->file_type, 'video'))
                                    <video class="img-fluid rounded" controls>
                                        <source src="{{ asset('storage/' . $file->file_path) }}"
                                            type="{{ $file->file_type }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Comment Section -->
                    @if ($newsfeed->comments)
                        <div id="comments-list-{{ $newsfeed->id }}">
                            @foreach ($newsfeed->comments as $comments)
                                @php
                                    $userHasReported = \App\Models\ReportedComment::where('comments_id', $comments->id)
                                        ->where('user_id', auth()->id())
                                        ->exists();

                                    $isDisabled = $userHasReported ? 'disabled' : '';
                                @endphp
                                <div class="d-flex">
                                    <img src="https://via.placeholder.com/40" class="rounded-circle me-2" height="40"
                                        alt="User Avatar">
                                    <div class="bg-light p-3 rounded-3 w-100">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $comments->user->firstname }}</strong>
                                            <small class="text-muted">{{ $comments->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0">{{ $comments->description }}</p>
                                        <div class="d-flex mt-2">
                                            <a class="like-comment ml-2" type="button"
                                                style="text-decoration: none; color: blue"
                                                data-comment-id="{{ $comments->id }}"
                                                onclick="toggleLike({{ $comments->id }})">
                                                <b><i class="far fa-thumbs-up"></i> Like</b>
                                            </a>
                                            <button class="border-0 bg-transparent text-danger" type="button"
                                                data-bs-toggle="modal" data-bs-target="#reportCommentModal"
                                                onclick="setCommentId({{ $comments->id }})" {{ $isDisabled }}>
                                                <i class="fas fa-flag"></i> {{ $userHasReported ? 'Reported' : 'Report' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <form id="comment-form-{{ $newsfeed->id }}" action="" method="POST">
                        @csrf
                        <input type="hidden" name="newsfeed_id" value="{{ $newsfeed->id }}">
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control" name="description"
                                placeholder="Comment as {{ auth()->user()->firstname }}">
                            <button class="btn btn-danger ms-2" type="submit"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                    <div class="d-flex justify-content-between mt-2">
                        <div>
                            <a href="#" class="btn btn-light"><i class="fas fa-thumbs-up"></i></a>
                            <a href="#" class="btn btn-light"><i class="fas fa-thumbs-down"></i></a>
                        </div>
                        <small class="text-muted">1 Likes • 0 Dislikes</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newsfeedModal" tabindex="-1" aria-labelledby="newsfeedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: #2E3236; color: white;">
                <form action="{{ route('newsfeed.store') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="newsfeedModalLabel" style="color: white;">Create post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="filter: invert(1);"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="your-avatar-url" class="rounded-circle" style="width: 40px; height: 40px;"
                                alt="Avatar">
                            <span class="ms-2 fw-bold">Dannel</span>
                        </div>

                        <div class="form-group mb-3">
                            <textarea class="form-control" name="description"
                                placeholder="What's on your mind, {{ auth()->user()->firstname }}?" rows="3"
                                style="background-color: #3E4348; color: white; border: none;"></textarea>
                        </div>


                        <input type="file" name="attachments[]" id="attachments" class="d-none" multiple
                            accept="image/*,video/*">

                        <div class="add-photos">
                            <button type="button" class="btn btn-secondary w-100 py-3"
                                style="background-color: #4B4F54; color: white; border: none;"
                                onclick="document.getElementById('attachments').click();">
                                <i class="fas fa-plus-circle"></i> Add Photos/Videos
                            </button>
                        </div>

                        <div id="preview" class="row mt-3"></div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-danger">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Report Comment Modal -->
    <div class="modal fade" id="reportCommentModal" tabindex="-1" aria-labelledby="reportCommentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportCommentModalLabel">Report Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="comments_id" id="commentId">

                        <div class="form-group">
                            <label for="reason">Reason for Reporting:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reasons[]" value="Nudity"
                                    id="nudity">
                                <label class="form-check-label" for="nudity">Nudity</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reasons[]" value="Hate Speech"
                                    id="hateSpeech">
                                <label class="form-check-label" for="hateSpeech">Hate Speech</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reasons[]" value="Threats"
                                    id="threats">
                                <label class="form-check-label" for="threats">Threats</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reasons[]" value="Vulgar"
                                    id="vulgar">
                                <label class="form-check-label" for="vulgar">Vulgar</label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input
                                <input class="form-check-input"
                                    type="checkbox" name="reasons[]" value="Abusive" id="abusive">
                                <label class="form-check-label" for="abusive">Abusive</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reasons[]" value="Violence"
                                    id="violence">
                                <label class="form-check-label" for="violence">Violence</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reasons[]" value="Others"
                                    id="others">
                                <label class="form-check-label" for="others">Others</label>
                            </div>

                            <div id="othersTextArea" class="form-group mt-3" style="display: none;">
                                <label for="otherReason">Please specify:</label>
                                <textarea class="form-control" name="other_reason" id="otherReason" rows="3" placeholder="Enter reason"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" form="reportForm">Submit Report</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setCommentId(commentId) {
            $('#commentId').val(commentId);
        }

        function toggleLike(commentId) {
            $.ajax({
                url: '{{ route('liked-comments') }}', // This points to the correct route for toggling likes
                method: 'POST',
                data: {
                    comments_id: commentId,
                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        // Update the button text or appearance based on the response
                        const likeButton = $('a[data-comment-id="' + commentId + '"]');
                        likeButton.html('<b><i class="far fa-thumbs-up"></i> ' + (response.liked ? 'Unlike' :
                            'Like') + '</b>');
                    } else {
                        alert('Failed to toggle like.');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred. Please try again.');
                    console.log(xhr.responseText); // Log the error for debugging
                }
            });
        }

        $(document).ready(function() {
            $('#attachments').on('change', function(event) {
                const preview = $('#preview');
                preview.empty(); // Clear previous previews
                const files = event.target.files;

                $.each(files, function(index, file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const mediaPreview = $('<div>', {
                            class: 'col-4 mb-3'
                        });

                        if (file.type.startsWith('image')) {
                            mediaPreview.html(
                                `<img src="${e.target.result}" class="img-fluid rounded">`);
                        } else if (file.type.startsWith('video')) {
                            mediaPreview.html(`
                            <video class="img-fluid rounded" controls>
                                <source src="${e.target.result}" type="${file.type}">
                            </video>`);
                        }
                        preview.append(mediaPreview);
                    };
                    reader.readAsDataURL(file);
                });
            });

            $('form[id^="comment-form-"]').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = form.serialize();
                var postID = form.find('input[name="newsfeed_id"]').val();

                $.ajax({
                    url: '{{ route('comments.store') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            var commentSection = $('#comments-list-' +
                                postID);
                            commentSection.append(response
                                .comment_html);
                            form.find('input[name="description"]').val(
                                '');
                        } else {
                            showSweetAlert("Error", response.message,
                                "error");
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred. Please try again.');
                        console.log(xhr.status);
                        console.log(xhr
                            .responseText);
                    }
                });
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#reportForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = form.serialize();

                $.ajax({
                    url: '{{ route('reported-comment.store') }}', // Ensure the correct route is defined
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to report comment.'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText); // Log the error for debugging
                        alert('An error occurred while reporting the comment.');
                    }
                });
            });

            $('#others').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#othersTextArea').show();
                } else {
                    $('#othersTextArea').hide();
                }
            });
        });
    </script>
@endpush
