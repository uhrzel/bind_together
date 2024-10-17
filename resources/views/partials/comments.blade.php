<div class="d-flex">
    <img src="https://via.placeholder.com/40" class="rounded-circle me-2" height="40" alt="User Avatar">
    <div class="bg-light p-3 rounded-3 w-100">
        <div class="d-flex justify-content-between">
            <strong>{{ $comment->user->firstname }}</strong>
            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
        </div>
        <p class="mb-0">{{ $comment->description }}</p>
        <div class="d-flex mt-2">
            <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#reportCommentModal" onclick="setCommentId({{ $comment->id }})">
                <i class="fas fa-flag"></i> Report
            </a>
        </div>
    </div>
</div>