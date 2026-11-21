<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasMasterActivityLog;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('transaction_hourly', key: 'id', incrementing: true)]
#[Fillable([
    'company_id',
    'datetime',
    'open',
    'high',
    'low',
    'close',
    'volume',
])]
class TransactionHourly extends Model
{
    use HasFactory, HasMasterActivityLog;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'datetime' => 'datetime',
            'open' => 'decimal:2',
            'high' => 'decimal:2',
            'low' => 'decimal:2',
            'close' => 'decimal:2',
            'volume' => 'integer',
        ];
    }

    /**
     * Get the company that owns the hourly transaction.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
