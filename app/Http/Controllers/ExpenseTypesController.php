<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseTypesController extends Controller
{
    /**
     * Display a listing of the expense types.
     */
    public function index()
    {
        $expenseTypes = ExpenseType::paginate(10);
        return view('admin.expense-types.index', compact('expenseTypes'));
    }

    /**
     * Show the form for creating a new expense type.
     */
    public function create()
    {
        return view('admin.expense-types.create');
    }

    /**
     * Store a newly created expense type in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:expense_types',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ExpenseType::create([
            'name' => $request->name,
        ]);

        return redirect()->route('expense-types.index')->with('success', 'Expense type created successfully');
    }

    /**
     * Show the form for editing the specified expense type.
     */
    public function edit(ExpenseType $expenseType)
    {
        return view('admin.expense-types.edit', compact('expenseType'));
    }

    /**
     * Update the specified expense type in storage.
     */
    public function update(Request $request, ExpenseType $expenseType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:expense_types,name,' . $expenseType->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $expenseType->update([
            'name' => $request->name,
        ]);

        return redirect()->route('expense-types.index')->with('success', 'Expense type updated successfully');
    }

    /**
     * Remove the specified expense type from storage.
     */
    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();
        return redirect()->route('expense-types.index')->with('success', 'Expense type deleted successfully');
    }
}