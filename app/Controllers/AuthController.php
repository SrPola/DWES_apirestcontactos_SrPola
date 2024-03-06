<?php
    namespace App\Controllers;

    use \Firebase\JWT\JWT;
    use App\Models\Usuarios;
    
    class AuthController {
        private $requestMethod;
        private $users;

        public function __construct($requestMethod) {
            $this->requestMethod = $requestMethod;
            $this->users = Usuarios::getInstancia();
        }

        public function loginFromRequest() {
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);

            $usuario = $input['usuario'];
            $dataUser = $this->users->login($usuario, $input['password']);
            if ($dataUser) {
                $issuer_claim = "http://unidad8apirestcontactos.local";
                $audience_claim = "http://unidad8apirestcontactos.local";
                $issuedat_claim = time();
                $notbefore_claim = time();
                $expire_claim = $issuedat_claim + 3600;
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "usuario" => $usuario,
                    )
                );

                $jwt = JWT::encode($token, KEY, 'HS256');
                $res = json_encode(
                    array(
                        "message" => "Login correcto",
                        "jwt" => $jwt,
                        "email" => $usuario,
                        "expireAt" => $expire_claim
                    )
                );

                $response['status_code_header'] = 'HTTP/1.1 201 Created';
                $response['body'] = $res;
            } else {
                $response['status_code_header'] = 'HTTP/1.1 401 Login failed';
                $response['body'] = null;
            } 
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
                return true;
            }
            return false;
        }
    }
