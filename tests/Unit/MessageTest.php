<?php


namespace Tests\Unit;

use Adiuta\SMS\Message;
use Tests\Support\UnitTester;

class MessageTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    /**
     * @var Message $message
     */
    private $message;

    protected function _before()
    {
        $this->message = new Message();
    }

    public function testFromNumberIsCorrectlySet()
    {
        $mobile = '255717000000';
        $this->message->setNumber($mobile);
        $this->tester->assertEquals($mobile, $this->message->getNumber());
    }

    public function testTextIsCorrectlySet()
    {
        $text = 'Hi, I am a test message';
        $this->message->setText($text);
        $this->tester->assertEquals($text, $this->message->getText());
    }

    public function testIDIsCorrectlySet()
    {
        $id = uniqid();
        $this->message->setId($id);
        $this->tester->assertEquals($id, $this->message->getId());
    }
}
