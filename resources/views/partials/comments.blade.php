<div class="d-flex">
    <img src="{{ asset('storage/' . $comment->user->avatar) }}" class="rounded-circle me-2" width="40" height="40"  alt="User Avatar">
    <div class="bg-light p-3 rounded-3 w-100">
        <div class="d-flex justify-content-between">
            <div>
                <strong>{{ $comment->user->firstname }} {{ $comment->user->lastname }}</strong>
                <a href="#" class="text-primary ms-2" onclick="editComment({{ $comment->id }})">Edit</a>
                <a href="#" class="text-danger ms-2" onclick="deleteComment({{ $comment->id }})">Delete</a>
            </div>
            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
        </div>
        <p class="mb-0">{{ $comment->description }}</p>
        <div class="d-flex mt-2">
            <a href="#" class="text-muted" onclick="toggleLike({{ $comment->id }})">
                <i class="far fa-thumbs-up"></i> Like
            </a>
        </div>
    </div>
</div>
