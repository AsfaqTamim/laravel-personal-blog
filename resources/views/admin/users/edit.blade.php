@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('topbar-title', 'Users')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Edit User</h1>
            <p class="page-sub">{{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">← Back to Users</a>
    </div>

    <div class="panel" style="max-width: 640px;">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                @include('admin.users._form')
                <button type="submit" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </div>
@endsection
