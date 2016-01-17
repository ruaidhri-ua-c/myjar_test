<?php

namespace Rory\MyJar_Test\App\Models;

class InterestMessage
{
    public $sum;
    public $days;
    public $interest;
    public $totalSum;
    public $token;
    
    private $isValid = false;

    public function __construct($queueMessageBody, $token = '')
    {
        $payload = json_decode($queueMessageBody);

        $isSumSetAndGretaterThanZero = isset($payload->sum) && $payload->sum > 0;
        $areDaysSetAndGreaterThanZero = isset($payload->days) && $payload->days > 0;
    
        if ($isSumSetAndGretaterThanZero && $areDaysSetAndGreaterThanZero) {
            $this->sum = $payload->sum;
            $this->days = $payload->days;
            $this->token = $token;
            $this->isValid = true;
        }
    }

    public function getInterest()
    {
        return $this->interest;
    }

    public function getTotalSum()
    {
        return $this->totalSum;
    }

    public function isValid()
    {
        return $this->isValid;
    }

    public function calculateInterestAmount()
    {
        $currentDay = 1;
        while ($currentDay <= $this->days) {
            $interestPercentage = $this->determineInterestPercentage($currentDay);
            $this->interest += round($this->sum * $interestPercentage / 100, 2);
            $currentDay++;
        }
    }

    public static function determineInterestPercentage($dayCount)
    {
        if ($dayCount % 3 === 0 && $dayCount % 5 === 0) {
            return 3;
        } elseif ($dayCount % 3 === 0) {
            return 1;
        } elseif ($dayCount % 5 === 0) {
            return 2;
        } else {
            return 4;
        }
    }

    public function calculateTotalSum()
    {
        $this->totalSum = round($this->sum + $this->interest, 2);
    }
}
