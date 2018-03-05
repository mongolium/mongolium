<?php

namespace Mongolium\Helper;

use Mongolium\Services\JsonResponse as JsonResponseObject;

trait JsonResponse
{
    public function jsonResponse($response, JsonResponseObject $jsonResponse)
    {
        $data = [
            'id' => $jsonResponse->getId(),
            'type' => $jsonResponse->getType(),
            'links' => $jsonResponse->getLinks()
        ];

        if (!$jsonResponse->isSuccess()) {
            $data['errors'] = [
                'code' => $jsonResponse->getCode(),
                'message' => $jsonResponse->getMessage()
            ];
        }
        elseif (!empty($jsonResponse->getData())) {
            $data['data'] = $jsonResponse->getData();
        }

        $response->withHeader('Content-type', 'application/json');
        return $response->withJson($data, $jsonResponse->getCode());
    }
}
