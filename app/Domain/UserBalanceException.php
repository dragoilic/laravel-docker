<?php

namespace App\Domain;

class UserBalanceException extends \DomainException
{
    public static function insufficientBalanceToMakeWithdrawal(int $withdrawalAmount, int $balance)
    {
        return new self(sprintf('You have insufficient funds (%s) to withdraw %s', $balance / 100, $withdrawalAmount / 100));
    }

    public static function insufficientBalanceToRedeem(int $redeemAmount, int $balance)
    {
        return new self(sprintf('You have insufficient funds (%s) to redeem %s', $balance , $redeemAmount ));
    }
}
