<?php

// 'require' is used over 'include' so that if the file cannot be found, a fatal error will be emitted as opposed to simply a warning
require "BankAccount.php";

// all classes extend the functionality of the BankAccount class
class ISA extends BankAccount
{

    // initilaise time period to 28 (days)
    public $TimePeriod = 28;

    // initiliased to null; the app can define this property at a later date
    public $AdditionalServices;

    // Methods

    // the logic in this method determines whether an ISA withdraw request is either accepted, accepted with a penalty, or denied. It does this by finding out when the last transaction happened, and checking if the current transaction violates the 28 day time period
    // the method overrides the WithDraw method in its parent class (BankAccount)
    public function WithDraw($amount)
    {

        $currentWithdrawalDate = new DateTime();

        // this variable will contain a number which tells us how many days there's been between the last withdrawal and the current withdrawal
        $days = null;

        // this variable contains the number of activites that have happened on the account (lock, unlock, withdraw etc)
        $length = count($this->Audit);

        // this loop iterates through the sub arrays in a particular account's audit array. The loop begins with the the length of the audit array and decrements, this is because we want to find latest withdrawal, not the earliest
        for ($i = $length; $i > 0; $i--) {

            // this variable stores each sub array in the audit array as it is iterated over. As arrays are 0 indexed, we minus 1 from each iteration to correctly get hold of the sub array
            $element = $this->Audit[$i - 1];

            // the 0 index of each sub array contains a string that describes the activity.
            if ($element[0] === "WITHDRAW ACCEPTED") {

                // this variable contains the date and time of the last accepted withdrawal, which we made to always be the fourth element in the sub array (so the third index)
                $lastWithdrawalDate = new DateTime($element[3]);

                // the days variable is now assigned a number which represents the number of days between the current withdrawal attempt and the last successful withdrawal. The 'format("%a")' part means the difference will be provided as number
                $days = $lastWithdrawalDate->diff($currentWithdrawalDate)->format("%a");

                // this break statement means that as soon as we find a sub array containing the message "WITHDRAW ACCEPTED", the for loop stops, as we no longer need to use it
                break;
            }
        }

        // if the condiitons in this first if statement are passed, then the account can have funds deducted successfully. The last condition checks whether the number of days since the last withdrawal is greater than the set time period (28 days)
        if ($days === null && $this->Locked === false || $this->Locked === false && $days > $this->TimePeriod) {

            $this->Balance -= $amount;

            array_push($this->Audit, array("WITHDRAW ACCEPTED", $amount, $this->Balance, $currentWithdrawalDate->format('c')));
        } else {

            // if previous conditions aren't met but the account is unlocked, funds are deducted with a penalty
            if ($this->Locked === false) {

                $this->Balance -= $amount;

                array_push($this->Audit, array("WITHDRAW ACCEPTED WITH PENALTY", $amount, $this->Balance, $currentWithdrawalDate->format('c')));

                // penalty method in this class is invoked, method is private as it is not needed anywhere else in the app
                $this->Penalty();
            } else {
                // if the account is locked, the withdrawal will be denied
                array_push($this->Audit, array("WITHDRAW DENIED", $amount, $this->Balance, $currentWithdrawalDate->format('c')));
            }
        }
    }

    private function Penalty()
    {

        $transDate = new DateTime();

        // 10 is minused from the account balance
        $this->Balance -= 10;

        // sub array is pushed onto the audit array
        array_push($this->Audit, array("WITHDRAW PENALTY", 10, $this->Balance, $transDate->format('c')));
    }

    public function GetBalance()
    {
        return $this->Balance;
    }
}

interface AccountPlus
{

    public function AddedBonus();
}

interface Savers
{

    public function OrderNewBook();
    public function OrderNewDepositBook();
}

class Savings extends BankAccount
{

    // these two variables keep track of pocket book and deposit book orders
    public $PocketBook = array();

    public $DepositBook = array();

    //Methods

    public function OrderNewBook()
    {

        $orderTime = new DateTime();

        array_push($this->PocketBook, "Ordered new pocket book on: " . $orderTime->format('c'));
    }

    public function OrderNewDepositBook()
    {

        $orderTime = new DateTime();

        array_push($this->DepositBook, "Ordered new deposit book on: " . $orderTime->format('c'));
    }
}

class Debit extends BankAccount
{

    private $CardNumber;

    private $SecurityCode;

    private $PinNumber;

    //Methods

    public function Validate()
    {

        $valDate = new DateTime();

        $this->CardNumber = rand(1000, 9999) . "-" . rand(1000, 9999) . "-" . rand(1000, 9999) . "-" . rand(1000, 9999);

        $this->SecurityCode = rand(100, 999);

        array_push($this->Audit, array("VALIDATED CARD", $valDate->format('c'), $this->CardNumber, $this->SecurityCode, $this->PinNumber));
    }

    public function ChangePin($newPin)
    {

        $pinChange = new DateTime();

        $this->PinNumber = $newPin;

        array_push($this->Audit, array("PIN CHANGED", $pinChange->format('c'), $this->PinNumber));
    }
}
