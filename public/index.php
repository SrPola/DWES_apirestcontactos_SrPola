<?php
    use App\Core\Router;
    use App\Controllers\ContactosController;
    use App\Controllers\AuthController;
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\Key;

    require "../bootstrap.php";
   
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header("Allow: GET, POST, PUT, DELETE"); 
    if ($requestMethod == 'OPTIONS') {
        die();
    }

    $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $uri = explode('/', $request);

    $id = null;
    if (isset($uri[2])) {
        $id = (int) $uri[2];
    }

    if (AUTH) {
        if ($request == "/login") {
            $auth = new AuthController($requestMethod);
            if (!$auth->loginFromRequest()) {
                exit(http_response_code(401));
            }
            die();
        }
        $autHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $arr = explode(" ", $autHeader);
        $jwt = $arr[1];

        if ($jwt) {
            try {
                $decoded = (JWT::decode($jwt, new Key(KEY, "HS256")));
            } catch (Exception $e) {
                echo json_encode(array(
                    "message" => "Access denied.",
                    "error" => $e->getMessage()
                ));
                exit(http_response_code(401));
            }
        }
        else {
            exit(http_response_code(401));
        }
    }

    $router = new Router();
    $router->add(array(
        "name" => "home",
        "path" => "/^\/contactos\/([0-9]+)?$/",
        "action" => ContactosController::class)
    );   

    $route = $router->match($request);
    
    if ($route) {
        $className = $route['action'];
        $controller = new $className($requestMethod, $id);
        $controller->processRequest();
    } else {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        echo json_encode($response);
    }
