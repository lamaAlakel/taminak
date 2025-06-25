<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_provider_id',
        'company_id',
    ];

    // Relationships

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
