<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Notice extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notices';

    public function created_by() {
        return $this->belongsTo(User::class, 'created_user_id')->withDefault();
    }

    protected function createdAt(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }
}