<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PayrollBenefit extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payslip_benefits';

    protected $fillable = ['payslip_id', 'name', 'amount', 'type', 'amount_type'];

    public function payroll() {
        return $this->belongsTo(Payroll::class, 'payslip_id')->withDefault();
    }

    protected function amount(): Attribute {
        $decimal_place = get_option('decimal_places', 2);

        return Attribute::make(
            get: fn($value) => number_format($value, $decimal_place, '.', ''),
        );
    }

}