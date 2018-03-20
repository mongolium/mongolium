<?php

namespace Mongolium\Core\Services\Response;

use Slim\Http\Response as SlimResponse;
use Mongolium\Core\Services\Response\Json;
use Mongolium\Core\Services\Response\Transformer;
use Mongolium\Core\Helper\Id;

class Response
{
    use Id;

    public function respond200(SlimResponse $response, string $id, string $type, array $data, array $links): SlimResponse
    {
        $transformer = $this->makeTransformer(
            200,
            'OK',
            $id,
            $type,
            $data,
            $links
        );

        return $this->respond($response, $transformer, 200);
    }

    public function respond201(SlimResponse $response, string $id, string $type, array $data, array $links): SlimResponse
    {
        $transformer = $this->makeTransformer(
            201,
            'CREATED',
            $id,
            $type,
            $data,
            $links
        );

        return $this->respond($response, $transformer, 201);
    }

    public function respond400(SlimResponse $response, string $message, array $links): SlimResponse
    {
        $transformer = $this->makeTransformer(
            400,
            'Bad Request: ' . $message,
            $this->uniqueId(),
            'error',
            [],
            $links
        );

        return $this->respond($response, $transformer, 400);
    }

    public function respond401(SlimResponse $response, string $message, array $links): SlimResponse
    {
        $transformer = $this->makeTransformer(
            401,
            'Unauthorized: ' . $message,
            $this->uniqueId(),
            'error',
            [],
            $links
        );

        return $this->respond($response, $transformer, 401);
    }

    public function respond404(SlimResponse $response, string $message, array $links): SlimResponse
    {
        $transformer = $this->makeTransformer(
            404,
            'NOT FOUND: ' . $message,
            $this->uniqueId(),
            'error',
            [],
            $links
        );

        return $this->respond($response, $transformer, 404);
    }

    public function makeJson(int $code, string $message, string $id, string $type, array $data, array $links): Json
    {
        return new Json($code, $message, $id, $type, $data, $links);
    }

    public function makeTransformer(int $code, string $message, string $id, string $type, array $data, array $links): Transformer
    {
        return new Transformer(
            $this->makeJson($code, $message, $id, $type, $data, $links)
        );
    }

    public function respond(SlimResponse $response, Transformer $transformer, int $code): SlimResponse
    {
        $response->withHeader('Content-type', 'application/json');

        return $response->withJson($transformer->getData(), $code);
    }

    public static function make(): self
    {
        return new Response;
    }
}
