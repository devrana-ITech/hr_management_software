<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBenefitDeduction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_benefits_deductions';

    protected $fillable = ['employee_id', 'name', 'amount', 'amount_type', 'type'];

}