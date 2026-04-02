<?php

namespace App\Repository;

use App\Model\Account;

class AccountRepository extends BaseRepository
{
    protected function __construct(Account $model)
    {
        $this->model = $model;
    }
}