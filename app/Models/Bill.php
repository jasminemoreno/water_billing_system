<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'meter_no',
        'consumption',
        'total',
        'due_date',
        'billing_date',
        'disconnection_date',
        'status',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'due_date' => 'date',
        'disconnection_date' => 'date',
    ];

    // A bill can have multiple payments over time
    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Check if bill is fully paid
    public function isPaid()
    {
        return $this->payments()->where('status', 'Verified')->exists();
    }

    // Check if bill has a pending payment
    public function isPending()
    {
        return $this->payments()->where('status', 'Pending')->exists();
    }
}