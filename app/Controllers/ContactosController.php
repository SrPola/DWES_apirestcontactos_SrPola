<?php

    namespace App\Controllers;

    use App\Models\Contactos;

    class ContactosController {
        private $requestMethod;
        private $contactosId;
        private $contactos;

        public function __construct($requestMethod, $contactosId) {
            $this->requestMethod = $requestMethod;
            $this->contactosId = $contactosId;
            $this->contactos = contactos::getInstancia();
        }

        public function processRequest() {
            switch ($this->requestMethod) {
                case 'GET':
                    if ($this->contactosId) {
                        $response = $this->getContactos($this->contactosId);
                    } else {
                        $response = $this->getAllContactos();
                    };
                    break;
                case 'POST':
                    $response = $this->createContactosFromRequest();
                    break;
                case 'PUT':
                    $response = $this->updateContactosFromRequest($this->contactosId);
                    break;
                case 'DELETE':
                    $response = $this->deleteContactos($this->contactosId);
                    break;
                default:
                    $response = $this->notFoundResponse();
                    break;
            }

            header($response['status_code_header']);

            if ($response['body']) {
                echo $response['body'];
            }
        }

        private function getAllContactos() {
            $result = $this->contactos->getAll();
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getContactos($id) {
            $result = $this->contactos->get($id);
            if (!$result) {
                return $this->notFoundResponse();
            }

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function updateContactosFromRequest($id) {
            $result = $this->contactos->get($id);
            if (!$result) {
                return $this->notFoundResponse();
            }

            $input = (array) json_decode(file_get_contents('php://input'), TRUE);
            $this->contactos->edit($id, $input);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['message' => 'Contacto actualizado']);
            return $response;
        }
        
        private function deleteContactos($id) {
            $result = $this->contactos->get($id);
            if (!$result) {
                return $this->notFoundResponse();
            }

            $this->contactos->delete($id);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['message' => 'Contacto eliminado']);
            return $response;
        }
        
        private function validateContactos($input) {
            if (!isset($input['nombre']) || !isset($input['telefono']) || !isset($input['email'])) {
                return false;
            }
            return true;
        }
        
        private function unprocessableEntityResponse() {
            $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
            $response['body'] = json_encode(['error' => 'Datos no válidos']);
            return $response;
        }
        
        private function notFoundResponse() {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = json_encode(['error' => 'Contacto no encontrado']);
            return $response;
        }
        
        private function createContactosFromRequest() {
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);
            if (!$this->validateContactos($input)) {
                return $this->unprocessableEntityResponse();
            }

            $this->contactos->set($input);
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode(['message' => 'Contacto creado']);
            return $response;
        }
        
        private function unproccesableEntyResponse() {
            $response["status_code_header"] = "HTTP/1.1 422 Unprocessable Entity";
            $response["body"] = json_encode(["error" => "Datos no válidos"]);
            return $response;
        }        
    }
