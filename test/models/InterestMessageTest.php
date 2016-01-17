<?php
require_once('app/models/InterestMessage.php');

use Rory\MyJar_Test\App\Models\InterestMessage as InterestMessage;

class InterestMessageTest extends PHPUnit_Framework_TestCase
{
    public function testInterestMessageInstantiatonWithValidData()
    {
        $jsonBody = '{"sum": 500, "days":3}';
        $im = new InterestMessage($jsonBody);
    
        $this->assertObjectHasAttribute('sum', $im);
        $this->assertObjectHasAttribute('days', $im);

        $this->assertEquals($im->sum, 500);
        $this->assertEquals($im->days, 3);

        $this->assertTrue($im->isValid());
    }

    public function testInterestMessageInstantiationWithInvalidData()
    {
        $jsonBody = '{"sum": null, "days": null}';
        $im = new InterestMessage($jsonBody);

        $this->assertFalse($im->isValid());
    }

    public function testInterestPercentage()
    {
        $this->assertEquals(InterestMessage::determineInterestPercentage(0), 3);
        $this->assertEquals(InterestMessage::determineInterestPercentage(1), 4);
        $this->assertEquals(InterestMessage::determineInterestPercentage(2), 4);
        $this->assertEquals(InterestMessage::determineInterestPercentage(3), 1);
        $this->assertEquals(InterestMessage::determineInterestPercentage(4), 4);
        $this->assertEquals(InterestMessage::determineInterestPercentage(5), 2);
        $this->assertEquals(InterestMessage::determineInterestPercentage(6), 1);
        $this->assertEquals(InterestMessage::determineInterestPercentage(10), 2);
        $this->assertEquals(InterestMessage::determineInterestPercentage(15), 3);
    }
    
    public function testInterestCalculation()
    {
        $jsonBody = '{"sum":123,"days":5}';
        $im = new InterestMessage($jsonBody);
    
        $im->calculateInterestAmount();
        $im->calculateTotalSum();
    
        $this->assertEquals($im->getInterest(), 18.45);
        $this->assertEquals($im->getTotalSum(), 141.45);
    }
}
