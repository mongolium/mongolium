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

        return array_merge($data, $this->makeData());
    }

    private function makeData(): array
    {
        if (!$this->json->isSuccess()) {
            return [
                'errors' => [
                    'code' => $this->json->getCode(),
                    'message' => $this->json->getMessage()
                ]
            ];
        }

        if (!empty($this->json->getData())) {
            return ['data' => $this->json->getData()];
        }

        return ['data' => []];
    }
}
