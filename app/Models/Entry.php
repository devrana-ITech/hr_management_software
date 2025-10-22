<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Entry extends Model {
    protected $fillable = ['transaction_id', 'account_id', 'type', 'amount'];

    public function account() {
        return $this->belongsTo(Account::class)->withDefault();
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class)->withDefault();
    }

}
