<?php

namespace App;

use App\Transaction;

class Buyer extends User
{
    
    // relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
