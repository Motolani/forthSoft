<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricePoint extends Model
{
    use HasFactory;
    protected $table = "price_point_table";

    protected $fillable = [
        'price',
        'service_id',
        'keyword_id',
        'period_type',
    ];
}
