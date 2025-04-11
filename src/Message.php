<?php

declare(strict_types=1);

namespace Adiuta\SMS;

use Adiuta\SMS\Interfaces\Base\MessageInterface;
use Closure;

/**
 * Class Message
 * Encapsulates the message details and provides methods to manipulate it.
 * @package Adiuta\SMS
 */
final class Message implements MessageInterface
{
    private string $id;
    private string $from;
    private string $text;

    public static function create(): MessageInterface
    {
        return new self();
    }

    public function setNumber(string $from): MessageInterface
    {
        $this->from = $from;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->from;
    }

    public function setText(string $text): MessageInterface
    {
        $this->text = $text;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setId(string $id): MessageInterface
    {
        $this->id = $id;
        return $this;
    }

    public function generateId(Closure $idGenerator): MessageInterface
    {
        $this->id  = $idGenerator->call($this);
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
