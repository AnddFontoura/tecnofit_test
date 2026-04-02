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
use App\Repository\AccountWithdrawPixRepository;
use App\Repository\AccountWithdrawRepository;
use Carbon\Carbon;
use Exception;
use Hyperf\Stringable\Str;
use InvalidArgumentException;

class AccountWithdrawService
{
    public function __construct(
        protected AccountRepository $accountRepository,
        protected AccountWithdrawRepository $accountWithdrawRepository,
        protected AccountWithdrawPixRepository $accountWithdrawPixRepository,
        protected AccountService $accountService,
    ) {
    }

    /**
     * @throws Exception
     */
    public function withdraw(array $requestDto): void
    {
        $account = $this->accountRepository->getById($requestDto['account_id']);

        if (! $account) {
            throw new Exception('Account not found');
        }

        $scheduled = isset($requestDto['schedule']);
        $scheduledFor = $requestDto['schedule'] ?? null;

        if ($scheduled) {
            $schedule = Carbon::parse($requestDto['schedule']);

            if ($schedule->isPast()) {
                throw new Exception('Scheduled withdrawal date is in the past');
            }
        }

        $createAccountWithdrawArray = [
            'id' => (string) Str::uuid(),
            'account_id' => $requestDto['account_id'],
            'method' => $requestDto['method'],
            'amount' => $requestDto['amount'],
            'scheduled' => $scheduled,
            'scheduled_for' => $scheduledFor,
            'done' => ! $scheduled,
            'error' => false,
            'error_reason' => null,
        ];

        $accountWithdraw = $this->accountWithdrawRepository->create($createAccountWithdrawArray);

        if (isset($requestDto['method'])) {
            $this->accountWithdrawPixRepository->create([
                'id' => (string) Str::uuid(),
                'account_withdraw_id' => $accountWithdraw->id,
                'type' => $requestDto['pix']['type'],
                'key' => $requestDto['pix']['key'],
            ]);
        }

        if (! $scheduled) {
            $this->updateBalance(
                $requestDto['account_id'],
                $requestDto['amount'],
                $accountWithdraw->id
            );
        }
    }

    public function storeError(string $accountWithdrawId, string $errorReason): void
    {
        $this->accountWithdrawRepository->update(
            [
                'done' => true,
                'error' => true,
                'error_reason' => $errorReason,
            ],
            $accountWithdrawId
        );
    }
    public function updateBalance(string $accountId, float $amount, string $accountWithdrawId): void
    {
        $account = $this->accountRepository->getById($accountId);

        if ($account->balance < $amount) {
            $this->storeError($accountWithdrawId, 'Insufficient balance');
            throw new InvalidArgumentException('Insufficient balance ' . $account->balance . ' < ' . $amount);
        }

        $this->accountRepository->update(
            [
                'balance' => $account->balance - $amount,
            ],
            $accountId
        );

        $this->accountWithdrawRepository->update(
            [
                'done' => true,
            ],
            $accountWithdrawId
        );
    }
}
