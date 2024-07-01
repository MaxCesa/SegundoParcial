<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "Token.php";
class ConfirmarPerfil
{

    public array $perfil = array();

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            $decoded = Token::DecodificarToken($token);
            if ($decoded['Mensaje'] == "OK" && in_array($decoded['Payload']->perfil, $this->perfil)) {
                $response = $handler->handle($request);
            } else {
                throw new Exception("Error en la decodificacion de TOKEN");
            }
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => $e->getMessage()));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function __construct($perfil)
    {
        $this->perfil = $perfil;
    }
}