<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_documents';

    public function staff(){
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

}