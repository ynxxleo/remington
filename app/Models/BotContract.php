<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotContract extends Model
{
    use HasFactory;

    protected $casts = [
        'funding_wallet_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => 'decimal:2',
        'profit' => 'decimal:2',
        'status' => 'integer',
        'result' => 'integer',
    ];

    public function legacyFundingWalletSymbol()
    {
        if (strtolower($this->pair) === 'stock') {
            return 'USDT';
        }

        $markets = json_decode(file_get_contents(resource_path('data/fxpairs.json')), true);
        foreach ($markets['data'] ?? [] as $market) {
            if (strtoupper($this->symbol) === strtoupper($market['symbol'])
                && strtoupper($this->pair) === strtoupper($market['pair'])) {
                return 'USDT';
            }
        }

        return $this->pair;
    }
}
