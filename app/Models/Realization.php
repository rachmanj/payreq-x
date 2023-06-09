<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Realization extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function payreq()
    {
        return $this->belongsTo(Payreq::class);
    }

    public function realizationDetails()
    {
        return $this->hasMany(RealizationDetail::class);
    }
}
