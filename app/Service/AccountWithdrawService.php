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

class AccountWithdrawService
{
    public function __construct(
        protected AccountRepository $accountRepository,
        protected AccountWithdrawRepository $accountWithdrawRepository,
        protected AccountWithdrawPixRepository $accountWithdrawPixRepository,
        protected AccountService $accountService,
        protected AccountWithdrawPixService $accountWithdrawPixService,
    ) {
    }

    /**
     * @throws Exception
     */
    public function withdraw(array $requestDto): void
    {
        $scheduled = isset($requestDto['schedule']);
        $scheduledFor = $requestDto['schedule'] ?? null;

        if ($scheduled) {
            $schedule = Carbon::parse($requestDto['schedule']);

            if ($schedule->isPast()) {
                throw new Exception('Scheduled withdrawal date is in the past');
            }
        }

        $createAccountWithdrawArray = [
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
                'account_withdraw_id' => $accountWithdraw->id,
                'type' => $requestDto['pix']['type'],
                'key' => $requestDto['pix']['key'],
            ]);
        }

        if (! $scheduled) {
            $this->accountService->updateBalance(
                $requestDto['account_id'],
                $requestDto['amount'],
                $accountWithdraw->id
            );
        }
    }

    public function storeError(int $accountWithdrawId, string $errorReason): void
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
}
