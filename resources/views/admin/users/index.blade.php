@extends('admin.layouts.app')

@section('title', 'Users')
@section('topbar-title', 'Users')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Users</h1>
            <p class="page-sub">{{ $users->total() }} user(s) registered.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">＋ New User</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $user->name }}</div>
                                @if ($user->id === auth()->id())
                                    <span class="badge badge-published">You</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline btn-sm">Edit</a>
                                @if ($user->id !== auth()->id())
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.destroy', $user) }}"
                                        class="inline-form"
                                        onsubmit="return confirm('Delete this user permanently?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-cell">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="panel-foot">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
