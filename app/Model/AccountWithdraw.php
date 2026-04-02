<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id 
 * @property string $account_id 
 * @property string $method 
 * @property string $amount 
 * @property int $scheduled 
 * @property string $scheduled_for 
 * @property int $done 
 * @property int $error 
 * @property string $error_reason 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class AccountWithdraw extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'account_withdraw';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'account_id',
        'method',
        'amount',
        'scheduled',
        'scheduled_for',
        'done',
        'error',
        'error_reason',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'scheduled' => 'integer',
        'done' => 'integer',
        'error' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
