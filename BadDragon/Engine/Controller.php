<?php
/*
+-------------------------------------------------------+
| Rajarshi Das                                          |
+-------------------------------------------------------+
| Created On:   18-Feb-2024                             |
| Updated On:   05-Nov-2025 ChatGPT                     |
+-------------------------------------------------------+
*/

namespace BadDragon;

class Controller
{
    public function __construct()
    {
        // Framework initialization (placeholder)
        return true;
    }

    /**
     * Load controller files (module, controller, and script)
     * for the given route.
     *
     * @param object $route
     * @return array|null
     */
    public function fire(object $route): ?array
    {
        // Define file paths
        $controllerModule = W3APP . '/Controller/' . $route->module . '/' . $route->module . '.php';
        $controllerMethod = W3APP . '/Controller/' . $route->module . '/' . $route->controller . '/' . $route->controller . '.php';
        $controllerScript = W3APP . '/Controller/' . $route->module . '/' . $route->controller . '/' . $route->method . '.php';

        // Check all files
        if (!is_file($controllerModule)) {
            return $this->handleMissing("Module", $controllerModule, $route->uri);
        }

        if (!is_file($controllerMethod)) {
            return $this->handleMissing("Controller", $controllerMethod, $route->uri);
        }

        if (!is_file($controllerScript)) {
            return $this->handleMissing("Script", $controllerScript, $route->uri);
        }

        // All good — return the framework stack
        return [
            $controllerModule,
            $controllerMethod,
            $controllerScript,
        ];
    }

    /**
     * Handles missing files with proper error messages.
     */
    private function handleMissing(string $type, string $file, string $uri): ?array
    {
        $msg = "Error[BD::Controller]: $type file missing: $file";

        if (defined('ENV') && ENV === 'dev') {
            die($msg);
        }

        if (function_exists('show404')) {
            show404("404: $msg");
        } else {
            die("404: $msg");
        }

        return null;
    }
}
