<?php
class Router {
    private $routes = [];
    
    public function addRoute($path, $controller, $method = 'GET') {
        $this->routes[] = [
            'path' => $path,
            'controller' => $controller,
            'method' => $method
        ];
    }
    
    public function dispatch($url) {
        $url = parse_url($url, PHP_URL_PATH);
        $url = trim($url, '/');
        $method = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->routes as $route) {
            $pattern = "#^" . preg_quote($route['path'], '#') . "/?$#i";
            
            if (preg_match($pattern, $url, $matches) && 
                strtoupper($route['method']) === $method) {
                
                array_shift($matches);
                
                if ($route['controller'] instanceof \Closure) {
                    call_user_func_array($route['controller'], $matches);
                    return;
                }
                
                if (is_string($route['controller'])) {
                    $parts = explode('@', $route['controller']);
                    if (count($parts) === 2) {
                        $controllerName = $parts[0];
                        $methodName = $parts[1];
                        
                        $controllerFile = "controllers/$controllerName.php";
                        if (file_exists($controllerFile)) {
                            require_once $controllerFile;
                            if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
                                $controller = new $controllerName();
                                call_user_func_array([$controller, $methodName], $matches);
                                return;
                            }
                        }
                    }
                }
                
                throw new \Exception("Invalid controller for route: {$route['path']}");
            }
        }
        
        header("HTTP/1.0 404 Not Found");
        if (file_exists('views/404.php')) {
            require 'views/404.php';
        } else {
            echo "Page not found";
        }
        exit();
    }
}