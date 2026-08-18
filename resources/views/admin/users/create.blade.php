@extends('admin.layouts.app')

@section('title', 'New User')
@section('topbar-title', 'Users')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Create User</h1>
            <p class="page-sub">Add a new user to the blog.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">← Back to Users</a>
    </div>

    <div class="panel" style="max-width: 640px;">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                @include('admin.users._form')
                <button type="submit" class="btn btn-primary">Save User</button>
            </form>
        </div>
    </div>
@endsection
