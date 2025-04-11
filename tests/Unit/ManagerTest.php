<?php


namespace Tests\Unit;

use Adiuta\SMS\Exceptions\InvalidNumberException;
use Adiuta\SMS\Exceptions\SendException;
use Adiuta\SMS\Interfaces\Base\GatewayInterface;
use Adiuta\SMS\Manager;
use Codeception\AssertThrows;
use Tests\Support\UnitTester;

class ManagerTest extends \Codeception\Test\Unit
{
    use AssertThrows;

    protected UnitTester $tester;

    private Manager $manager;
    private $messages = [];

    protected function _before()
    {
        $this->messages[] = [
            'from' => '255717000000',
            'text' => 'Hi, I am a test message',
            'id' => "1",
        ];
        $this->messages[] = [
            'from' => '255717000001',
            'text' => 'Hi, I am a test message2',
            'id' => "2",
        ];

        $gateway = $this->makeEmpty(GatewayInterface::class, [
            'sendMessage' => function ($message) {
                foreach ($this->messages as $m) {
                    if ($m['id'] == $message->getId()) {
                        throw new SendException('Message is already sent!');
                    }
                }
                $count =  count($this->messages);
                return array_push($this->messages, $message) == ($count + 1);
            },
            'sendMessages' => function ($messages) {
                $ids = [];
                foreach ($this->messages as $m) {
                    $ids[] = $m['id'];
                }

                foreach ($messages as $m) {
                    if (in_array($m->getId(), $ids)) {
                        throw new SendException('Message is already sent!');
                    }
                }

                $count =  count($this->messages);
                foreach ($messages as $message) {
                    array_push($this->messages, $message);
                }
                return count($this->messages) == ($count + count($messages));
            },
            'getStatus' => function ($id) {
                foreach ($this->messages as $message) {
                    if ($message['id'] === $id) {
                        return GatewayInterface::STATUS_DELIVERED;
                    }
                }
                return GatewayInterface::STATUS_NOTFOUND;
            },
            'getError' => function () {
                if (count($this->messages) > 2) {
                    return null;
                }
                return 'No messages found';
            },
            'setLogger' => function ($logger) {}
        ]);

        $this->manager = new Manager($gateway, function () {
            return strval(hrtime(true));
        });
    }

    // tests
    public function testSendOneSuccessfully()
    {
        $this->tester->assertTrue($this->manager->send('255717000000', 'TZ', 'Send Hi!'));
    }

    public function testSendManySuccessfully()
    {
        $this->tester->assertTrue($this->manager->sendMultiple(['255717000000', '255717000001'], 'TZ', 'Send Hi!'));
    }

    public function testSendOneFailWithInvalidNumber()
    {
        $this->assertThrowsWithMessage(InvalidNumberException::class, 'Invalid mobile number: 011011', function () {
            $this->manager->send('011011', 'TZ', 'Fail with invalid number');
        });
    }

    public function testSendManyFailWithInvalidNumber()
    {
        $this->assertThrowsWithMessage(InvalidNumberException::class, 'Invalid mobile number: 011011', function () {
            $this->manager->sendMultiple(['0717600600', '011011', '0717001001'], 'TZ', 'Fail with invalid number');
        });
    }

    public function testGetStatusWithCorrectId()
    {
        $status = $this->manager->getStatus("1");
        $this->tester->assertEquals(GatewayInterface::STATUS_DELIVERED, $status);
    }

    public function testGetStatusWithIncorrectId()
    {
        $status = $this->manager->getStatus("11111");
        $this->tester->assertEquals(GatewayInterface::STATUS_NOTFOUND, $status);
    }

    public function testGetError()
    {
        $this->tester->assertNotNull($this->manager->getLastError());
        $this->tester->assertEquals('No messages found', $this->manager->getLastError());
        //remove error by adding a message (see the mockup)
        $this->manager->send('0717000001', 'TZ', 'Trigger that error!');
        $this->tester->assertNull($this->manager->getLastError());
    }
}
