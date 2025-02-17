<?php
require_once './models/Usuario.php';
class UsuarioController
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        Usuario::RegistrarUsuario($parametros['usuario'], $parametros['clave'], $parametros['perfil'], $parametros['email'], $_FILES['foto']);
        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}