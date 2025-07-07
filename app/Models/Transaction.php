<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'total_amount',
        'payment_method',
        'payment_proof',
        'status',
        'admin_notes',
        'receipt_path',
        'signature_hash',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getFormattedTotal()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getStatusBadge()
    {
        $badges = [
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'completed' => 'bg-info',
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    public function approve($adminId, $notes = null)
    {
        $this->status = 'approved';
        $this->approved_by = $adminId;
        $this->approved_at = now();
        $this->admin_notes = $notes;
        $this->save();

        foreach ($this->items as $item) {
            $item->product->reduceStock($item->quantity);
        }
    }

    public function reject($adminId, $notes = null)
    {
        $this->status = 'rejected';
        $this->approved_by = $adminId;
        $this->admin_notes = $notes;
        $this->save();
    }

    public static function generateTransactionCode()
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'TRX' . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}