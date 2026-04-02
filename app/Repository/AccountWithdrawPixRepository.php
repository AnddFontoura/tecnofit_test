<?php

namespace App\Repository;

use App\Model\AccountWithdrawPix;

class AccountWithdrawPixRepository extends BaseRepository
{
    protected function __construct(AccountWithdrawPix $model)
    {
        $this->model = $model;
    }
}