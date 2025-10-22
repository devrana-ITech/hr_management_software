<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    protected $fillable = ['description', 'transaction_date', 'created_user_id', 'system_generated', 'attachment', 'parent_id'];

    public function entries() {
        return $this->hasMany(Entry::class);
    }

    public function created_by() {
        return $this->belongsTo(User::class, 'created_user_id')->withDefault();
    }

    public function updated_by() {
        return $this->belongsTo(User::class, 'updated_user_id')->withDefault();
    }

    protected function transactionDate(): Attribute {
        $date_format = get_date_format();

        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::parse($value)->format("$date_format"),
        );
    }

}
