<?php /*
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On:   18-Feb-2024                             |
| Updated On:                                           |
+-------------------------------------------------------+
*/

namespace BadDragon;

class Controller
{

    public function __construct()
    {
        // die("Hello from bad-dragon");
        return true;
    }

    public function fire(object $route): ?array
    {
        $controllerModule = W3APP . '/Controller/' . $route->module . '/' . $route->module . '.php';
        $controllerMethod = W3APP . '/Controller/' . $route->module . '/' . $route->controller . '/' . $route->controller . '.php';
        $controllerScript = W3APP . '/Controller/' . $route->module . '/' . $route->controller . '/' . $route->method . '.php';

        if (!is_file($controllerModule)) {
            if (ENV == 'dev') {
                die("Module " . $controllerMethod . " files missing...");
            } else {
                show404("Module | " . $route->uri);
            };
        };

        if (!is_file($controllerMethod)) {
            if (ENV == 'dev') {
                die("Controller Method " . $controllerMethod . " is missing...");
            } else {
                show404("Method | " . $route->uri);
            };
        };

        if (!is_file($controllerScript)) {
            if (ENV == 'dev') {
                die("Controller Method Script " . $controllerScript . " is missing...");
            } else {
                show404("Script | " . $route->uri);
            };
        };

        $framework = [
            $controllerModule,
            $controllerMethod,
            $controllerScript
        ];

        return $framework;
    }
}
