@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-field">
    <label for="name">Name</label>
    <input
        type="text"
        id="name"
        name="name"
        class="form-input"
        value="{{ old('name', $user->name) }}"
        placeholder="Clara Dawson"
        required
        maxlength="255"
    >
</div>

<div class="form-field">
    <label for="email">Email Address</label>
    <input
        type="email"
        id="email"
        name="email"
        class="form-input"
        value="{{ old('email', $user->email) }}"
        placeholder="clara@example.com"
        required
        maxlength="255"
    >
</div>

<div class="form-field">
    <label for="password">Password <small>({{ $user->exists ? 'leave blank to keep current' : 'minimum 8 characters' }})</small></label>
    <input
        type="password"
        id="password"
        name="password"
        class="form-input"
        placeholder="••••••••"
        {{ $user->exists ? '' : 'required' }}
    >
</div>

<div class="form-field">
    <label for="password_confirmation">Confirm Password</label>
    <input
        type="password"
        id="password_confirmation"
        name="password_confirmation"
        class="form-input"
        placeholder="••••••••"
        {{ $user->exists ? '' : 'required' }}
    >
</div>
