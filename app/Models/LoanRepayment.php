<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model {

    public function loan() {
        return $this->belongsTo(EmployeeLoan::class, 'loan_id')->withDefault();
    }

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    protected function principleAmount(): Attribute {
        $decimal_place = get_option('decimal_places', 2);

        return Attribute::make(
            get: fn(string $value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function interest(): Attribute {
        $decimal_place = get_option('decimal_places', 2);

        return Attribute::make(
            get: fn(string $value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function createdAt(): Attribute {
        $date_format = get_date_format();

        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::parse($value)->format("$date_format"),
        );
    }
}
