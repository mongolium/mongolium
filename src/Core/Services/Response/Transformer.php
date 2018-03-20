<?php

namespace Mongolium\Core\Services\Response;

use Mongolium\Core\Services\Response\Json;

class Transformer
{
    private $json;

    public function __construct(Json $json)
    {
        $this->json = $json;
    }

    public function getData(): array
    {
        $data = [
            'id' => $this->json->getId(),
            'type' => $this->json->getType(),
            'links' => $this->json->getLinks()
        ];

        if (!$this->json->isSuccess()) {
            $data['errors'] = [
                'code' => $this->json->getCode(),
                'message' => $this->json->getMessage()
            ];
        } elseif (!empty($this->json->getData())) {
            $data['data'] = $this->json->getData();
        }

        return $data;
    }
}
