<?php


namespace App;


class App
{
    private $routes = [];
    private $services = [];
    private $serviceNames = [];
    private $serviceInit = [];

    public function run()
    {
        // Роутер
        $basepath = preg_replace('#/[^/]+$#', '', $_SERVER["SCRIPT_NAME"]);
        define('BASE', $basepath . '/');
        define('BASENS', $basepath);
        if (
            !isset($_SERVER["REDIRECT_URL"])
            || 0 !== stripos($_SERVER["REDIRECT_URL"], $basepath, 0)
        ) die(header('HTTP/1.0 500 Internal Server Error') . 'Apache configuration error');

        $resolvedPath = substr($_SERVER["REDIRECT_URL"], strlen($basepath));
        $verb = $_SERVER['REQUEST_METHOD'];
        $resolvedPathVerb = "$verb $resolvedPath";
        $success = false;
        foreach ($this->routes as $route => $controllerRoute) {
            list($controllerClass, $controllerMethod) = $controllerRoute;
            if (!preg_match("#^$route$#", $resolvedPathVerb, $regexParams)) continue;
            array_shift($regexParams);
            array_unshift($regexParams, $resolvedPath);
            array_unshift($regexParams, $verb);

            foreach ($this->serviceInit as $serviceKey) {
                $this->service($serviceKey)->init();
            }

            $controller = new $controllerClass;
            $controller->setApp($this);
            echo call_user_func_array([$controller, $controllerMethod], $regexParams);
            $success = true;
            break;
        }
        if (!$success) {
            header("HTTP/1.0 404 Not Found");
            echo "Not Found: " . htmlspecialchars($resolvedPath);
        }
    }

    public function registerRoute($path, $controllerClass, $controllerMethod = 'index')
    {
        $newPath = preg_replace('#\\{[^}]*\\}#', '([^/]*+)',
            preg_replace('#^([|A-Z0-9_-]+) #', '(?:$1) ',
                $path
            )
        );
        $this->routes[$newPath] = [$controllerClass, $controllerMethod];
    }

    public function registerService($name, $class, $init = false)
    {
        $this->serviceNames[$name] = $class;
        if ($init) {
            $this->serviceInit[] = $name;
        }
    }

    public function service($name)
    {
        $serviceClass = $this->serviceNames[$name];
        if (!array_key_exists($serviceClass, $this->services)) {
            $service = new $serviceClass;
            $service->setApp($this);
            $this->services[$serviceClass] = $service;
        }
        return $this->services[$serviceClass];
    }
}