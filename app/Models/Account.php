<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model {
    use HasFactory;

    public function entries() {
        return $this->hasMany(Entry::class);
    }

    // Method to calculate account balance
    public function balance($toDate) {
        $debits = $this->entries()
            ->whereHas('transaction', function (Builder $query) use ($toDate) {
                $query->where('transaction_date', '<=', $toDate);
            })
            ->where('type', 'debit')->sum('amount');

        $credits = $this->entries()
            ->whereHas('transaction', function (Builder $query) use ($toDate) {
                $query->where('transaction_date', '<=', $toDate);
            })
            ->where('type', 'credit')->sum('amount');

        if ($this->type == 'asset' || $this->type == 'expense') {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }

}
