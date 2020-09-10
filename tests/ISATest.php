<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require "src/SubClasses.php";

class ISATest extends TestCase
{
    public function testCreateISAAccount(): void
    {
        $Account = new ISA;

        $Account->APR = 5.0;
        $Account->SortCode = "20-20-20";
        $Account->FirstName = "Adam";
        $Account->LastName = "Gardner";
        $Account->AdditionalServices = "holiday package";

        $this->assertInstanceOf(ISA::class, $Account);
        $this->assertSame(5.0, $Account->APR);
        $this->assertSame("20-20-20",  $Account->SortCode);
        $this->assertSame("Adam", $Account->FirstName);
        $this->assertSame("Gardner", $Account->LastName);
        $this->assertSame("holiday package", $Account->AdditionalServices);
    }

    public function testCannotWithdrawFundsFromLockedAccount(): void
    {
        $Account = new ISA;

        $Account->Deposit(1000);
        $Account->Lock();
        $Account->WithDraw(500);

        $this->assertSame(1000, $Account->GetBalance());
    }

    public function testPenaltyDeducted(): void
    {
        $Account = new ISA;

        $Account->Deposit(1000);
        $Account->WithDraw(100);
        $Account->WithDraw(100);

        $this->assertSame(790, $Account->GetBalance());
    }
}
