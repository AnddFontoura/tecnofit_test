<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property string $account_withdraw_id 
 * @property string $type 
 * @property string $key 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class AccountWithdrawPix extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'account_withdraw_pix';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'account_withdraw_id',
        'type',
        'key'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
}
