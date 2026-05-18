<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UzasboImportModel extends Model
{
    use HasFactory;

    protected $table = 'uzasbo_imports';

    protected $fillable = [
        'account_number',
        'full_name',
        'department',
        'bank_schot',
        'inventory_number',
        'old_inventory_number',
        'expense_article',
        'name',
        'unit',
        'opening_quantity',
        'opening_summ',
        'debit_quantity',
        'debit_summ',
        'credit_quantity',
        'credit_summ',
        'closing_quantity',
        'closing_summ',
        'import_type',
        'row_number',
        'status',
    ];

    protected $casts = [
        'opening_quantity' => 'decimal:4',
        'opening_summ'     => 'decimal:2',
        'debit_quantity'   => 'decimal:4',
        'debit_summ'       => 'decimal:2',
        'credit_quantity'  => 'decimal:4',
        'credit_summ'      => 'decimal:2',
        'closing_quantity' => 'decimal:4',
        'closing_summ'     => 'decimal:2',
    ];
    public function scopeNotTransferred(Builder $query): void
    {
        $query->where(function (Builder $q) {
            $q->whereNull('status')->orWhere('status', '!=', 'TRANSFERED');
        });
    }

    public function item(): HasOne
    {
        return $this->hasOne(Item::class, 'uzasbo_import_id');
    }
}
