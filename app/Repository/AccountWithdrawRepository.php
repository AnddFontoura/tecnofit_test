<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Repository;

use App\Model\AccountWithdraw;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;

class AccountWithdrawRepository extends BaseRepository
{
    protected mixed $model = AccountWithdraw::class;

    public function getScheduledForNow(): Collection
    {
        return $this->model
            ->where('scheduled_at', '=', Carbon::now('Y-m-d h:i'))
            ->get();
    }
}
