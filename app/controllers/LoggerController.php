<?php
require_once './models/Logger.php';
require_once './models/Token.php';

class LoggerController extends Logger
{
  public function LogIn($request, $response, $args)
  {

    $parametros = $request->getParsedBody();


    $log = new Logger();
    $log->username = $parametros['username'];
    $log->pw = $parametros['pw'];
    $resultado = $log->log_in();
    if ($resultado) {
      $token = Token::CodificarToken($resultado['usuario'], $resultado['perfil'], $resultado['id']);
      $payload = json_encode(array("mensaje" => "OK token: " . $token));
      $_SESSION['jwt_token'] = $token;
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    } else {
      $payload = json_encode(array("mensaje" => "Usuario o contraseña incorrectos"));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
  }
}