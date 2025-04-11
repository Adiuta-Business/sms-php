<?php

declare(strict_types=1);

namespace Adiuta\SMS\Interfaces\Base;

use Adiuta\SMS\Message;
use Closure;

interface MessageInterface
{
    /**
     * Set the contact number to send the message to.
     * @param string $from The contact number to send the message to.
     */
    public function setNumber(string $from): MessageInterface;

    /**
     * Get the contact number to send the message to.
     * @return string|null The contact number to send the message to.
     */
    public function getNumber(): ?string;

    /**
     * Set the message text.
     * @param string $text The message text.
     */
    public function setText(string $text): MessageInterface;

    /**
     * Get the message text.
     * @return string The message text.
     */
    public function getText(): string;

    /**
     * Set the message ID.
     * @param string $id The message ID.
     */
    public function setId(string $id): MessageInterface;

    /**
     * Get the message ID.
     * @return string|null The message ID.
     */
    public function getId(): ?string;

    /**
     * Generates message ID and sets as current message ID
     * @param Closure $idGenerator the function that returns string ID
     * eg. $ig =  function() { return microtime(); }
     */
    public function generateId(Closure $idGenerator): MessageInterface;

    /**
     * Short hand of creating instance
     */
    public static function create(): MessageInterface;
}
