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

namespace App\Service;

use App\Repository\AccountRepository;
use App\Repository\AccountWithdrawRepository;
use InvalidArgumentException;

class AccountService
{
    public function __construct(
        protected AccountRepository $accountRepository,
        protected AccountWithdrawRepository $accountWithdrawRepository,
        protected AccountWithdrawService $accountWithdrawService,
    ) {
    }

    public function updateBalance(string $accountId, float $amount, int $accountWithdrawId): void
    {
        $account = $this->accountRepository->getById($accountId);

        if ($account->amount < $amount) {
            $this->accountWithdrawService->storeError($accountWithdrawId, 'Insufficient balance');
            throw new InvalidArgumentException('Insufficient balance');
        }

        $this->accountRepository->update(
            [
                'amount' => $account->amount - $amount,
            ],
            $accountId
        );
    }
}
