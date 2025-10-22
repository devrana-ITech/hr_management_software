<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class EmployeeExpense extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_expenses';

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    public function category() {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id')->withDefault();
    }

    protected function amount(): Attribute {
        $decimal_place = get_option('decimal_places', 2);

        return Attribute::make(
            get: fn(string $value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    public function created_by() {
        return $this->belongsTo(User::class, 'created_user_id')->withDefault();
    }

    public function updated_by() {
        return $this->belongsTo(User::class, 'updated_user_id')->withDefault(['name' => _lang('N/A')]);
    }

    protected function createdAt(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get: fn(string $value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }

    protected function updatedAt(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get: fn(string $value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }

    protected function transDate(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get: fn(string $value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }

}