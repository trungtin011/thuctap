<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'financial_record_id',
        'expense_type_id',
        'amount',
        'description',
    ];

    public function financialRecord()
    {
        return $this->belongsTo(FinancialRecord::class);
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }
    public function submittedBy()
{
    return $this->belongsTo(User::class, 'submitted_by');
}

}