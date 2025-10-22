<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmployeeLoan extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_loans';

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    public function loan_type() {
        return $this->belongsTo(LoanType::class, 'loan_type_id')->withDefault();
    }

    protected function applicationDate(): Attribute {
        $date_format = get_date_format();

        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::parse($value)->format("$date_format"),
        );
    }

    public function created_by() {
        return $this->belongsTo(User::class, 'created_user_id')->withDefault();
    }
}