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
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" width="50" height="50"
                        class="rounded-circle me-3" alt="User Avatar">
                </div>
                <div class="col-11 mt-1">
                    <input type="text" style="cursor: pointer" class="form-control rounded-pill me-2"
                        data-bs-toggle="modal" data-bs-target="#newsfeedModal"
                        placeholder="What's on your mind, {{ auth()->user()->firstname }}?">
                </div>
                {{-- <div class="text-end mt-2">
                    <button class="btn btn-danger rounded-pill "><i class="fas fa-image"></i> Create Post</button>
                </div> --}}
            </div>
        </div>

        @foreach ($newsfeeds->reverse() as $newsfeed)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex">
                        <img src="{{ asset('storage/' . $newsfeed->user->avatar) }}" width="50" height="50" class="rounded-circle me-3"
                            alt="User Avatar">
                        <div>
                            <h6 class="mb-0">{{ $newsfeed->user->firstname }} {{ $newsfeed->user->lastname }}</h6>
                            <small>{{ $newsfeed->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            @if ($newsfeed->user_id == auth()->id())
                                <li>
                                    <button type="button" class="dropdown-item editBtn" data-bs-toggle="modal"
                                        data-bs-target="#editPostModal" data-id="{{ $newsfeed->id }}">
                                        Edit
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger deleteBtn" data-bs-toggle="modal"
                                        data-bs-target="#deleteNewsfeedModal" data-id="{{ $newsfeed->id }}">
                                        Delete
                                    </button>
                                </li>
                            @else
                                @student
                                    <li><button type="button" class="dropdown-item reportBtn" data-bs-toggle="modal"
                                            data-bs-target="#reportPostModal" data-id="{{ $newsfeed->id }}">Report</button></li>
                                @endstudent
                                @admin_org
                                    <li><button type="button" class="dropdown-item reportBtn" data-bs-toggle="modal"
                                            data-bs-target="#reportPostModal" data-id="{{ $newsfeed->id }}">Report</button>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ route('deactivate-post', ['newsfeedId' => $newsfeed->id]) }}">Deactivate</a>
                                    </li>
                                @endadmin_org
                                @admin_sport
                                    <li><button type="button" class="dropdown-item reportBtn" data-bs-toggle="modal"
                                            data-bs-target="#reportPostModal" data-id="{{ $newsfeed->id }}">Report</button>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ route('deactivate-post', ['newsfeedId' => $newsfeed->id]) }}">Deactivate</a>
                                    </li>
                                @endadmin_sport
                                @adviser
                                    <li><button type="button" class="dropdown-item reportBtn" data-bs-toggle="modal"
                                            data-bs-target="#reportPostModal" data-id="{{ $newsfeed->id }}">Report</button>
                                    </li>
                                    {{-- <li><a class="dropdown-item"
                                            href="{{ route('deactivate-post', ['newsfeedId' => $newsfeed->id]) }}">Deactivate</a>
                                    </li> --}}
                                @endadviser
                                @coach
                                    <li><button type="button" class="dropdown-item reportBtn" data-bs-toggle="modal"
                                            data-bs-target="#reportPostModal" data-id="{{ $newsfeed->id }}">Report</button>
                                    </li>
                                    {{-- <li><a class="dropdown-item"
                                            href="{{ route('deactivate-post', ['newsfeedId' => $newsfeed->id]) }}">Deactivate</a>
                                    </li> --}}
                                @endcoach
                            @endif
                        </ul>
                    </div>
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
                                    <img src="{{ asset('storage/' . $comments->user->avatar) }}"
                                        class="rounded-circle me-2" height="40" width="40" alt="User Avatar">
                                    <div class="bg-light p-3 rounded-3 w-100">
                                        <form id="editCommentForm-{{ $comments->id }}" action="" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="d-flex justify-content-between">
                                                <div id="comment-display-{{ $comments->id }}">
                                                    <strong>{{ $comments->user->firstname }}
                                                        {{ $comments->user->lastname }}</strong>
                                                    @if ($comments->user_id == auth()->id())
                                                        <button type="button"
                                                            class="border-0 bg-transparent text-primary ms-2 text-info edit-btn"
                                                            data-id="{{ $comments->id }}"
                                                            id="edit-btn-{{ $comments->id }}">Edit</button>
                                                        <button type="submit" hidden
                                                            class="border-0 bg-transparent text-primary ms-2 text-info"
                                                            data-id="{{ $comments->id }}"
                                                            id="saveButton-{{ $comments->id }}">Save</button>
                                                        <button class="border-0 bg-transparent text-danger ms-2 delete-btn"
                                                            data-bs-toggle="modal" data-bs-target="#deleteCommentModal"
                                                            data-id="{{ $comments->id }}">Delete</button>
                                                    @endif
                                                </div>
                                                <small
                                                    class="text-muted">{{ $comments->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0 comment-text" id="comment-text-{{ $comments->id }}">
                                                {{ $comments->description }}</p>
                                            <textarea name="description" class="form-control comment-textarea" id="comment-textarea-{{ $comments->id }}"
                                                rows="2" hidden>{{ $comments->description }}</textarea>
                                        </form>
                                        <div class="d-flex mt-2">
                                            @if ($comments->user_id != auth()->id())
                                                <a class="like-comment ml-2" type="button"
                                                    style="text-decoration: none; color: blue"
                                                    data-comment-id="{{ $comments->id }}"
                                                    onclick="toggleLike({{ $comments->id }})">
                                                    <b><i class="far fa-thumbs-up"></i> Like</b>
                                                </a>
                                                <button class="border-0 bg-transparent text-danger" type="button"
                                                    data-bs-toggle="modal" data-bs-target="#reportCommentModal"
                                                    onclick="setCommentId({{ $comments->id }})" {{ $isDisabled }}>
                                                    <i class="fas fa-flag"></i>
                                                    {{ $userHasReported ? 'Reported' : 'Report' }}
                                                </button>
                                            @endif
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
                            <button class="btn btn-danger ms-2" type="submit"><i
                                    class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                    {{-- <div class="d-flex justify-content-between mt-2">
                        <div>
                            <a href="#" class="btn btn-light"><i class="fas fa-thumbs-up"></i></a>
                            <a href="#" class="btn btn-light"><i class="fas fa-thumbs-down"></i></a>
                        </div>
                        <small class="text-muted">1 Likes • 0 Dislikes</small>
                    </div> --}}
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
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle"
                                style="width: 40px; height: 40px;" alt="Avatar">
                            <span class="ms-2 fw-bold">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</span>
                        </div>

                        <div class="form-group mb-3">
                            <textarea class="form-control" name="description"
                                placeholder="What's on your mind, {{ auth()->user()->firstname }}?" rows="3"
                                style="background-color: #3E4348; color: white; border: none;"></textarea>
                        </div>


                        <input type="file" name="attachments[]" id="attachments" class="d-none" multiple>

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


    <!-- Report Post Modal -->
    <div class="modal fade" id="reportPostModal" tabindex="-1" aria-labelledby="reportPostModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportPostModalLabel">Report Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportPostForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="newsfeed_id" id="newsfeedReportId">

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
                                    id="othersPost">
                                <label class="form-check-label" for="others">Others</label>
                            </div>

                            <div id="othersPostTextArea" class="form-group mt-3" style="display: none;">
                                <label for="otherReason">Please specify:</label>
                                <textarea class="form-control" name="other_reason" id="otherReason" rows="3" placeholder="Enter reason"></textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Submit Report</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- delete modal --}}
    <div class="modal fade" id="deleteNewsfeedModal" tabindex="-1" aria-labelledby="deleteNewsfeedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="" id="newsfeedDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteNewsfeedModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this post?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- delete comment modal --}}
    <div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="" id="commentDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCommentModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this comment?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Edit Post Modal -->
    <div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: #2E3236; color: white;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPostModalLabel">Edit Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="filter: invert(1);"></button>
                </div>
                <div class="modal-body">
                    <form id="editPostForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <textarea class="form-control" name="description" id="description" rows="3"
                                style="background-color: #3E4348; color: white; border: none;"></textarea>
                        </div>

                        <div id="editPostFiles" class="mb-3 row">
                        </div>
                        <input type="hidden" name="deleted_files" id="deletedFiles" value="">

                        <div class="add-photos">
                            <input type="file" name="attachments[]" id="editAttachments" class="d-none" multiple>
                            <button type="button" class="btn btn-secondary w-100 py-3"
                                style="background-color: #4B4F54; color: white; border: none;"
                                onclick="document.getElementById('editAttachments').click();">
                                <i class="fas fa-plus-circle"></i> Add Photos/Videos
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editPostForm" class="btn btn-danger">Save Changes</button>
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
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
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

        const deletedFiles = [];

        function removeFile(fileId) {
            deletedFiles.push(fileId);

            $('#deletedFiles').val(deletedFiles.join(','));

            $('.file-preview-' + fileId).hide();
        }

        $(document).ready(function() {

            $('.edit-btn').on('click', function(e) {
                e.preventDefault();

                var commentId = $(this).data('id');

                // Update the form action dynamically
                var form = $('#editCommentForm-' + commentId);
                form.attr('action', '/comments/' + commentId);

                // Show the save button, hide the edit button
                $('#edit-btn-' + commentId).hide();
                $('#saveButton-' + commentId).removeAttr('hidden');

                // Hide the comment text and show the textarea
                $('#comment-text-' + commentId).hide();
                $('#comment-textarea-' + commentId).removeAttr('hidden');
            });

            // Handle form submission via AJAX
            $('form[id^="editCommentForm"]').on('submit', function(e) {
                e.preventDefault(); // Prevent the form from reloading the page

                var form = $(this);
                var commentId = form.find('.edit-btn').data('id'); // Get comment ID from button

                // Perform the AJAX request
                $.ajax({
                    url: form.attr('action'), // Get the form action dynamically
                    type: 'POST',
                    data: form.serialize(), // Serialize the form data
                    success: function(response) {
                        if (response.success) {
                            // Update the comment text without reloading
                            var updatedText = $('#comment-textarea-' + commentId).val();
                            $('#comment-text-' + commentId).text(updatedText)
                                .show(); // Update and show the <p>
                            $('#comment-textarea-' + commentId).hide(); // Hide the textarea
                            $('#saveButton-' + commentId).attr('hidden',
                                true); // Hide save button
                            $('#edit-btn-' + commentId).show(); // Show the edit button
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            });

            $('.reportBtn').click(function() {
                $('#newsfeedReportId').val($(this).data('id'))
                $('#reportPostForm').attr('action', '/reported-post/')
            })

            $('.editBtn').click(function() {
                fetch('/newsfeed/' + $(this).data('id'))
                    .then(response => response.json())
                    .then(newsfeed => {
                        $('#description').val(newsfeed.description)

                        var filesContainer = $('#editPostFiles');
                        // filesContainer.empty();

                        if (Array.isArray(newsfeed.newsfeed_files)) {
                            newsfeed.newsfeed_files.forEach(function(file) {
                                if (file.file_type.startsWith('image')) {
                                    filesContainer.append(`
                                    <div class="col-4 mb-3 position-relative file-preview-${file.id}">
                                    <img src="/storage/${file.file_path}" alt="file" class="img-fluid rounded">
                                    <button type="button" class="btn btn-danger position-absolute top-0 start-100 translate-middle" style="border-radius: 50%; padding: 4px 8px; font-size: 14px;" onclick="removeFile(${file.id})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `);
                                }
                            });
                        }

                        $('#editPostForm').attr('action', '/newsfeed/' + $(this).data('id'));
                    })
            })

            $('.deleteBtn').click(function() {
                $('#newsfeedDelete').attr('action', '/newsfeed/' + $(this).data('id'))
            })

            $('.delete-btn').click(function() {
                $('#commentDelete').attr('action', '/comments/' + $(this).data('id'))
            })

            $('#attachments').on('change', function(event) {
    const preview = $('#preview');
    preview.empty(); // Clear previous previews
    const files = event.target.files; // Retrieve all selected files

    $.each(files, function(index, file) {
        const reader = new FileReader();
        
        // Generate file label with the actual file name
        const fileName = file.name;
        
        // Handle file load
        reader.onload = function(e) {
            const mediaPreview = $('<div>', {
                class: 'col-4 mb-3'
            });

            // Append the file name before the media preview
            mediaPreview.append(`<p class="text-white">${fileName}</p>`);

            // Check the file type for image or video and create corresponding preview elements
            if (file.type.startsWith('image')) {
                mediaPreview.append(`<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">`);
            } else if (file.type.startsWith('video')) {
                mediaPreview.append(`
                    <video class="img-fluid rounded" controls style="max-height: 150px; object-fit: cover;">
                        <source src="${e.target.result}" type="${file.type}">
                    </video>`);
            }

            // Append the media preview to the preview container
            preview.append(mediaPreview);
        };

        // Read the file as a data URL
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

            $('#othersPost').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#othersPostTextArea').show();
                } else {
                    $('#othersPostTextArea').hide();
                }
            });
        });
    </script>
@endpush
