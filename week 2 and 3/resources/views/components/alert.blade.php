@props(['type' => 'success'])

@if(session()->has($type))
    <div class="alert alert-{{ $type }}" role="status">
        {{ session($type) }}
    </div>
@endif
