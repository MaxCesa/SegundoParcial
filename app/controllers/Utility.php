<?php

class Utilities
{
    public static function ParametrosInvalidos($response)
    {
        $payload = json_encode(array("mensaje" => "Parametros invalidos"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}