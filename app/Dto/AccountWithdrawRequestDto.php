<?php

namespace App\Dto;

class AccountWithdrawRequestDto
{
    public function __construct(
        public string $accountId,
        public string $method,
        public string $pixType,
        public string $pixKey,
        public float $amount,
        public ?string $schedule,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            accountId: $data['account_id'],
            method: $data['method'],
            pixType: $data['pix']['type'],
            pixKey: $data['pix']['key'],
            amount: (float) $data['amount'],
            schedule: $data['schedule'] ?? null,
        );
    }
}