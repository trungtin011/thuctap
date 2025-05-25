@extends('layouts.admin')

@section('title', 'Edit Expense Type')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Edit Expense Type</h2>
    <form method="POST" action="{{ route('expense-types.update', $expenseType) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">Expense Type Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $expenseType->name) }}">
            @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
        <a href="{{ route('expense-types.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
    </form>
</div>
@endsection