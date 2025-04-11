<?php

declare(strict_types=1);

namespace Adiuta\SMS;

use Adiuta\SMS\Exceptions\InvalidNumberException;
use Adiuta\SMS\Interfaces\Base\GatewayInterface;
use Closure;
use Psr\Log\LoggerInterface;

final class Manager
{
    private GatewayInterface $gateway;
    private ?LoggerInterface $logger;
    private bool $isLoggingEnabled = false;

    private Closure $idGenerator;

    /**
     * @var GatewayInterface $gateway the sending gateway
     * @var LoggerInterface $logger the logger
     */
    public function __construct(GatewayInterface $gateway, Closure $idGenerator, ?LoggerInterface $logger =  null)
    {
        $this->gateway = $gateway;
        $this->idGenerator = $idGenerator;
        $this->logger = $logger;
        if (!empty($logger)) {
            $this->isLoggingEnabled = true;
            $gateway->setLogger($logger);
        }
    }

    /**
     * Send a message using the configured gateway.
     *
     * @param string $to The recipient's phone number.
     * @param string $message The message to send.
     * @return bool True on success, false on failure.
     */
    public function send(string $mobile, string $country, string $message): bool
    {
        //normalize the phone number
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneNumberUtil->parse($mobile, $country);
        if ($phoneNumberUtil->isValidNumber($phoneNumber)) {
            $normalizedNumber = $phoneNumberUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164);
            return $this->gateway->sendMessage(
                Message::create()
                    ->generateId($this->idGenerator)
                    ->setNumber($normalizedNumber)
                    ->setText($message)
            );
        } else {
            if ($this->isLoggingEnabled) {
                $this->logger->error("Invalid phone number: {$mobile}");
            } else {
                throw new InvalidNumberException($mobile);
            }
            return false;
        }
    }

    /**
     * Send multiple messages using the configured gateway.
     *
     * @param string[] $numbers An array of contacts to send to.
     * @param string $text The message to send.
     * @return bool True on success, false on failure.
     */
    public function sendMultiple(array $numbers, string $country,  string $text): bool
    {
        if (empty($numbers)) {
            if ($this->isLoggingEnabled) {
                $this->logger->error("No phone numbers provided.");
            }
            return false;
        }

        $validNumbers = [];
        foreach ($numbers as $number) {
            $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phoneNumber = $phoneNumberUtil->parse($number, $country);
            if ($phoneNumberUtil->isValidNumber($phoneNumber)) {
                $normalizedNumber = $phoneNumberUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164);
                $validNumbers[] = $normalizedNumber;
            } else {
                if ($this->isLoggingEnabled) {
                    $this->logger->error("Invalid phone number: {$number}");
                } else {
                    throw new InvalidNumberException($number);
                }
                return false;
            }
        }

        $messages = [];
        foreach ($validNumbers as $number) {
            $messages[] = Message::create()
                ->generateId($this->idGenerator)
                ->setNumber($number)
                ->setText($text);
        }
        return $this->gateway->sendMessages($messages);
    }

    /**
     * Get the status of a message.
     *
     * @param string $id The message ID.
     * @return int The status of the message.
     */
    public function getStatus(string $id): int
    {
        return $this->gateway->getStatus($id);
    }

    /**
     * Get the error message if the message failed to send.
     * Call it after sendMessage() or sendMessages() to get the error message.
     *
     * @return string|null The error message, or null if there was no error.
     */
    public function getLastError(): ?string
    {
        return $this->gateway->getError();
    }
}
