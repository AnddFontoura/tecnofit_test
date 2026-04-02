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

namespace App\Controller;

use App\Request\AccountWithdrawRequest;
use App\Service\AccountService;
use App\Service\AccountWithdrawPixService;
use App\Service\AccountWithdrawService;
use Exception;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use function Hyperf\Support\make;

class AccountWithdrawController extends AbstractController
{
    protected AccountWithdrawService $accountWithdrawService;

    protected AccountService $accountService;

    protected AccountWithdrawPixService $accountWithdrawPixService;

    public function __construct(
    ) {
        parent::__construct();
        $this->accountWithdrawService = make(AccountWithdrawService::class);
    }

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }

    /**
     * @throws Exception
     */
    public function withdraw(
        AccountWithdrawRequest $request,
        RequestInterface $response,
        string $accountUuid
    ): array {
        $dto = array_merge(
            [
                'account_id' => $accountUuid,
            ],
            $request->validated()
        );

        $this->accountWithdrawService->withdraw($dto);

        return [
            'message' => 'Withdraw request created',
        ];
    }
}
