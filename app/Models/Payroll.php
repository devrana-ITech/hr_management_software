<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payslips';

    public function staff() {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    public function payroll_benefits() {
        return $this->hasMany(PayrollBenefit::class, 'payslip_id');
    }

    protected function currentSalary(): Attribute {
        $decimal_place = get_option('decimal_places', 2);

        return Attribute::make(
            get: fn($value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function expense(): Attribute {
        $decimal_place = get_option('decimal_places', 2);

        return Attribute::make(
            get: fn($value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function loan(): Attribute {
        $decimal_place = get_option('decimal_places', 2);

        return Attribute::make(
            get: fn($value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function absenceFine(): Attribute {
        $decimal_place = get_option('decimal_places', 2);

        return Attribute::make(
            get: fn($value) => number_format($value, $decimal_place, '.', ''),
        );
    }
}