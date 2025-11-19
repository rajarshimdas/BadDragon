<?php
/*
+-------------------------------------------------------+
| Rajarshi Das                                          |
+-------------------------------------------------------+
| Created On:   29-Jan-2024                             |
| Updated On:   05-Nov-2025 ChatGPT                     |
|               18-Nov-2025 Bugfix handleGetRoute       |
+-------------------------------------------------------+
*/

namespace BadDragon;

use BadDragon\Controller;

class Router extends Controller
{
    public string $a = '';
    public string $uri = '';
    public string $module = '';
    public string $controller = '';
    public string $method = '';
    public array $parts = [];
    private array $aroute = [];

    public function __construct()
    {
        // Handle POST or GET routes
        if (!empty($_POST['a'])) {
            $this->handlePostRoute($_POST['a']);
        } else {
            $this->handleGetRoute();
        }
    }

    /**
     * Handle POST requests and auto-route based on `a` parameter.
     */
    private function handlePostRoute(string $action): void
    {
        $this->a = $action;
        $this->uri = "POST:" . $action;

        $parts = explode('-', $action);

        if (count($parts) < 3) {
            show404("Invalid routing info for POST request.");
        }

        $this->parts = $parts;
        [$this->module, $this->controller, $this->method] = [
            ucfirst($parts[0]),
            ucfirst($parts[1]),
            $parts[2],
        ];
    }

    /**
     * Handle GET requests and route to correct controller/method.
     */
    private function handleGetRoute(): void
    {
        $routesFile = W3APP . '/Routes.php';

        if (!is_file($routesFile)) {
            show404('Custom Routes file missing.');
        }

        require_once $routesFile;
        // var_dump($rx);

        // From NGINX
        $x = trim($_SERVER['REQUEST_URI'], '/');

        // Routing decision
        if (empty($x)) {
            // Use default Route
            $rxURI = $rx['static'][$rx['default']];
        } elseif (!empty($rx['static'][$x])) {
            // Use static Route
            $rxURI = $rx['static'][$x];
        } else {
            // Use Autoroute
            $rxURI = "/$x";
        };
        // rx($rxURI);

        if (!alpha_numeric_dash_slash($rxURI)) {
            show404("Invalid URI");
        }

        $this->uri = $rxURI;
        $routeParts = array_values(array_filter(explode('/', $this->uri)));

        if (count($routeParts) < 3) {
            show404("404! That route was not found.");
        }

        $this->parts = $routeParts;
        $this->aroute = array_slice($routeParts, 0, 3);

        $this->autoroute();
    }

    /**
     * Parse and assign module, controller, and method.
     */
    private function autoroute(): void
    {
        if (empty($this->aroute) || count($this->aroute) < 3) {
            header("Location:" . BASE_URL . "/home");
            exit;
        }

        [$module, $controller, $method] = $this->aroute;

        $this->module = ucfirst($module);
        $this->controller = ucfirst($controller);
        $this->method = $method;
    }
}
