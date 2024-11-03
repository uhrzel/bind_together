{{-- user info and avatar --}}
<div class="avatar av-l chatify-d-flex">
    <img src="{{ $avatar }}" alt="User Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
</div>
<p class="info-name">{{ $full_name }}</p>
<div class="messenger-infoView-btns">
    <a href="#" class="danger delete-conversation">Delete Conversation</a>
</div>
{{-- shared photos --}}
<div class="messenger-infoView-shared">
    <p class="messenger-title"><span>Shared Photos</span></p>
    <div class="shared-photos-list"></div>
</div>
