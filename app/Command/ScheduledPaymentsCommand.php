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

namespace App\Command;

use App\Repository\AccountWithdrawRepository;
use App\Service\AccountService;
use App\Service\AccountWithdrawService;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Crontab\Annotation\Crontab;
use Psr\Container\ContainerInterface;

#[Crontab(
    rule: '* * * * *',
    name: 'scheduled_withdraw',
    callback: 'execute',
)]
class ScheduledPaymentsCommand extends HyperfCommand
{
    public function __construct(
        protected ContainerInterface $container,
        protected AccountService $accountService,
        protected AccountWithdrawService $accountWithdrawService,
        protected AccountWithdrawRepository $accountWithdrawRepository,
    ) {
        parent::__construct('command:scheduled-payments');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Process Scheduled Payments');
    }

    public function handle()
    {
        $accountWithdraws = $this->accountWithdrawRepository->getScheduledForNow();

        foreach ($accountWithdraws as $accountWithdraw) {
            $this->accountService->updateBalance(
                $accountWithdraw->account_id,
                $accountWithdraw->amount,
                $accountWithdraw->id
            );
        }
    }
}
