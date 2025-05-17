@extends('layouts.admin')

@section('title', 'Expense Types')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Expense Types</h2>
        <a href="{{ route('expense-types.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New</a>
    </div>
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if ($expenseTypes->isEmpty())
        <p class="text-gray-600">No expense types found.</p>
    @else
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2 text-left">Name</th>
                    <th class="border px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenseTypes as $expenseType)
                    <tr>
                        <td class="border px-4 py-2">{{ $expenseType->name }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('expense-types.edit', $expenseType) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('expense-types.destroy', $expenseType) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-2" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection