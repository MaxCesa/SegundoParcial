<?php
require_once './models/Usuario.php';
class UsuarioController
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if (
            isset($parametros['perfil']) && $parametros['perfil'] != "" &&
            isset($parametros['usuario']) && $parametros['usuario'] != "" &&
            isset($parametros['clave']) && $parametros['clave'] != "" &&
            isset($parametros['email']) && $parametros['email'] != "" &&
            isset($_FILES['foto']) && $_FILES['foto'] != ""
        ) {
            Usuario::RegistrarUsuario($parametros['usuario'], $parametros['clave'], $parametros['perfil'], $parametros['email'], $_FILES['foto']);
            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("mensaje" => "Ocurrio un error"));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
    }
}