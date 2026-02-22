<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'currency_symbol',
        'currency_symbol_placement',
        'logo',
        'timezone',
        'tax_number',
        'tax_percentage',
        'default_profit_percent',
        'start_date',
        'fy_start_month',
        'stock_accounting_method',
        'transaction_edit_days',
        'date_format',
        'time_format',
        'currency_precision',
        'quantity_precision',
        'toast_position',
        'is_setup_completed',
    ];

    protected $casts = [
        'is_setup_completed'     => 'boolean',
        'tax_percentage'         => 'decimal:2',
        'default_profit_percent' => 'decimal:2',
        'start_date'             => 'date',
        'fy_start_month'         => 'integer',
        'transaction_edit_days'  => 'integer',
        'currency_precision'     => 'integer',
        'quantity_precision'     => 'integer',
    ];

    /**
     * Get all locations for this business.
     */
    public function locations()
    {
        return $this->hasMany(BusinessLocation::class);
    }
}
