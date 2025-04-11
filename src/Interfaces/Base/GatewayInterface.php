<?php

declare(strict_types=1);

namespace Adiuta\SMS\Interfaces\Base;

use Psr\Log\LoggerInterface;

interface GatewayInterface
{
    const STATUS_PENDING = 100;
    const STATUS_SENT = 200;
    const STATUS_DELIVERED = 300;
    const STATUS_FAILED = 400;
    const STATUS_NOTFOUND = 404;
    const STATUS_UNKNOWN = 500;

    /**
     * Send a message to a contact.
     *
     * @param MessageInterface $message message to send 
     * @return bool
     * @throws \Adiuta\SMS\Exceptions\SendException
     */
    public function sendMessage(MessageInterface $message): bool;

    /**
     * Send multiple messages to multiple contacts.
     *
     * @param array $messages array of messages to send
     * @return bool
     * @throws \Adiuta\SMS\Exceptions\SendException
     */
    public function sendMessages(array $messages): bool;

    /**
     * Get the status of a message.
     *
     * @param string $id The message ID.
     * @return int The status of the message.
     * @throws \Adiuta\SMS\Exceptions\MessageNotFound
     */
    public function getStatus(string $id): int;

    /**
     * Get the error message if the message failed to send.
     *
     * @return string|null The error message, or null if there was no error.
     */
    public function getError(): ?string;

    /**
     * Set loggeer to bee used by gateway in logging messages
     */
    public  function setLogger(LoggerInterface  $logger): void;
}
