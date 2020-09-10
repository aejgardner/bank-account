<?php
// a series of sub classes will inherit this class, each representing a type of bank account. Objects will then be instantiated from those sub classes
abstract class BankAccount
{

    protected $Balance = 0;

    public $APR;

    public $SortCode;

    public $FirstName;

    public $LastName;

    public $Audit = array();

    protected $Locked = false;

    // Methods

    // public method means method can be used by instantiated objects
    public function WithDraw($amount)
    {

        // get the current date and time to add to the audit info when a withdraw is attempted
        $transDate = new DateTime();

        if ($this->Locked === false) {
            $this->Balance -= $amount;
            // below you are pushing an array into the audit array, the elements of the pushed array contain different information about the withdrawal
            array_push($this->Audit, array("WITHDRAW ACCEPTED", $amount, $this->Balance, $transDate->format('c')));
        } else {
            array_push($this->Audit, array("WITHDRAW DENIED", $amount, $this->Balance, $transDate->format('c')));
        }
    }

    // deposit method is basically the same as withdraw but adds to the balance property, rather than subtracting from it
    public function Deposit($amount)
    {

        $transDate = new DateTime();

        if ($this->Locked === false) {

            $this->Balance += $amount;

            array_push($this->Audit, array("DEPOSIT ACCEPTED", $amount, $this->Balance, $transDate->format('c')));
        } else {
            array_push($this->Audit, array("DEPOSIT DENIED", $amount, $this->Balance, $transDate->format('c')));
        }
    }

    // both the lock and unlock methods push an array onto the audit array, each containing a string describing the action and the date/time it happened
    public function Lock()
    {

        $this->Locked = true;

        $lockedDate = new DateTime();

        array_push($this->Audit, array("Account Locked", $lockedDate->format('c')));

        return $this;
    }

    public function Unlock()
    {

        $this->Locked === false;

        $unlockedDate = new DateTime();

        array_push($this->Audit, array("Account Unlocked", $unlockedDate->format('c')));

        return $this;
    }
}
