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
        'submitted_form',
        'status',
    ];

    protected $casts = [
        'submitted_form'   => 'array',
        'status' => 'string',
    ];

    // Relationships
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
