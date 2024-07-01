<?php

require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Token
{

    private static $key = "tiendaropa";

    private static $token = array(
        "iat" => "", //Cuándo fue metido
        "nbf" => "", //Antes de esto no va a funcionar (Desde)
        "exp" => "", //Hasta cuando va a funcionar
        "usuario" => "",
        "perfil" => "",
        "id" => "",
    );

    public static function CodificarToken($usuario, $perfil, $id)
    {
        $ahora = time();
        $fecha = new Datetime("now", new DateTimeZone('America/Buenos_Aires'));
        Token::$token["iat"] = $ahora;
        Token::$token["nbf"] = $ahora;
        Token::$token["exp"] = $ahora + (360060);
        Token::$token["usuario"] = $usuario;
        Token::$token["perfil"] = $perfil;
        Token::$token["id"] = $id;
        $jwt = JWT::encode(Token::$token, Token::$key, "HS256");

        return $jwt;
    }

    public static function DecodificarToken($token)
    {
        try {
            $payload = JWT::decode($token, new Key(Token::$key, "HS256"));
            $decoded = array("Estado" => "OK", "Mensaje" => "OK", "Payload" => $payload);
        } catch (\Firebase\JWT\BeforeValidException $e) {
            $mensaje = $e->getMessage();
            $decoded = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } catch (\Firebase\JWT\ExpiredException $e) {
            $mensaje = $e->getMessage();
            $decoded = array("Estado" => "ERROR", "Mensaje" => "$mensaje.");
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $mensaje = $e->getMessage();
            $decoded = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $decoded = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        }
        return $decoded;
    }


}
