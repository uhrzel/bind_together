<div class="favorite-list-item">
    @if($user)
        <div data-id="{{ $user->id }}" data-action="0" class="avatar av-m">
            <img src="{{ $avatar }}" alt="User Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
        </div>
        <p>{{ strlen($user->name) > 5 ? substr($user->name,0,6).'..' : $user->name }}</p>
    @endif
</div>
