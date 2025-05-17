@extends('layouts.admin')

@section('title', 'Platforms Management')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6">Platforms Management</h2>
    <a href="{{ route('platforms.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create New Platform</a>
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white rounded shadow">
    <thead>
        <tr>
            <th class="px-4 py-2 text-center">ID</th>
            <th class="px-4 py-2 text-center">Name</th>
            <th class="px-4 py-2 text-center">Created At</th>
            <th class="px-4 py-2 text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($platforms as $platform)
        <tr class="border-t">
            <td class="px-4 py-2 text-center">{{ $platform->id }}</td>
            <td class="px-4 py-2 text-center">{{ $platform->name }}</td>
            <td class="px-4 py-2 text-center">
                {{ $platform->created_at ? $platform->created_at->format('d/m/Y H:i') : 'N/A' }}
            </td>
            <td class="px-4 py-2 text-center">
                <a href="{{ route('platforms.edit', $platform->id) }}" class="text-blue-600 hover:underline">Edit</a>
                <form action="{{ route('platforms.destroy', $platform->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this platform?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline ml-2">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="px-4 py-2 text-center">No platforms found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

    <div class="mt-4">
        {{ $platforms->links() }}
    </div>
</div>
@endsection