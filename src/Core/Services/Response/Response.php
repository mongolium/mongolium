<?php

namespace Mongolium\Core\Services\Response;

use Slim\Http\Response as SlimResponse;
use Mongolium\Core\Services\Response\Json;
use Mongolium\Core\Services\Response\Transformer;
use Mongolium\Core\Helper\Id;

class Response
{
    use Id;

    private static function getInstance()
    {
        return new Response();
    }

    public static function respond200(SlimResponse $response, string $id, string $type, array $data, array $links): SlimResponse
    {
        $instance = static::getInstance();

        $transformer = $instance->makeTransformer(
            200,
            'OK',
            $id,
            $type,
            $data,
            $links
        );

        return $instance->respond($response, $transformer, 200);
    }

    public static function respond201(SlimResponse $response, string $id, string $type, array $data, array $links): SlimResponse
    {
        $instance = static::getInstance();

        $transformer = $instance->makeTransformer(
            201,
            'CREATED',
            $id,
            $type,
            $data,
            $links
        );

        return $instance->respond($response, $transformer, 201);
    }

    public static function respond400(SlimResponse $response, string $message, array $links): SlimResponse
    {
        $instance = static::getInstance();

        $transformer = $instance->makeTransformer(
            400,
            'Bad Request: ' . $message,
            $instance->uniqueId(),
            'error',
            [],
            $links
        );

        return $instance->respond($response, $transformer, 400);
    }

    public static function respond401(SlimResponse $response, string $message, array $links): SlimResponse
    {
        $instance = static::getInstance();

        $transformer = $instance->makeTransformer(
            401,
            'Unauthorized: ' . $message,
            $instance->uniqueId(),
            'error',
            [],
            $links
        );

        return $instance->respond($response, $transformer, 401);
    }

    public static function respond404(SlimResponse $response, string $message, array $links): SlimResponse
    {
        $instance = static::getInstance();

        $transformer = $instance->makeTransformer(
            404,
            'NOT FOUND: ' . $message,
            $instance->uniqueId(),
            'error',
            [],
            $links
        );

        return $instance->respond($response, $transformer, 404);
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
}
