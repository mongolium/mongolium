<?php

namespace Mongolium\Core\Services\Response;

class Json
{
    private $code;

    private $message;

    private $id;

    private $type;

    private $links;

    private $data;

    public function __construct(int $code, string $message, string $id, string $type, array $data, array $links)
    {
        $this->code = $code;

        $this->message = $message;

        $this->id = $id;

        $this->type = $type;

        $this->data = $data;

        $this->links = $links;
    }

    public function isSuccess(): bool
    {
        return $this->code === 200 || $this->code === 201;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getLinks(): array
    {
        return $this->links;
    }
}
