<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanRequest extends Model
{
    use HasFactory;

    protected $table = 'plan_requests';

    protected $fillable = [
        'user_id',
        'plan_id',
        'data',
        'status',
    ];

    protected $casts = [
        'data'   => 'array',
        'status' => 'string',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
