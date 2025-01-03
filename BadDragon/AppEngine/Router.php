<?php /*
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On:   29-Jan-2024                             |
| Updated On:                                           |
+-------------------------------------------------------+
*/

namespace BadDragon;

use BadDragon\Controller;

class Router extends Controller
{
    public $a;
    public $uri;
    public $module;
    public $controller;
    public $method;
    public $parts;

    public function __construct()
    {
        if (isset($_POST["a"])) {

            // POST Method
            $this->a = $_POST["a"];

            /* Auto routing */
            $p = explode("-", $this->a);
            $this->parts = [
                ((isset($p[0]) ? $p[0] : 'x')),
                ((isset($p[1]) ? $p[1] : 'x')),
                ((isset($p[2]) ? $p[2] : 'x')),
            ];

            // Parse Module | Controller | Method for this request
            $this->autoroute();
        } else {

            // Read Routes defination
            $routesFile = W3APP . '/Routes.php';
            if (is_file($routesFile)) {
                require_once $routesFile;
            } else {
                die('Error: Custom Routes file missing.');
            }

            //var_dump($rx);

            // REQUEST URI (GET Requests)
            // var_dump($_SERVER["REQUEST_URI"]);
            $uri = (rtrim($_SERVER["REQUEST_URI"], "/") != null) ? rtrim($_SERVER["REQUEST_URI"], "/") : "/" . $rx['default'];
            // die($uri);

            /* Validate URI */
            if (!alpha_numeric_dash_slash($uri)) {
                show404("Invalid URI");
            }


            /* Parts in route */
            $p = explode("/", $uri);
            // var_dump($p);

            $co = isset($p) ? count($p) : 0;
            $matchflag = 0;

            if ($co >= 3) {
                if (isset($rx["static"][$p[1] . '/' . $p[2]])) {
                    // die("static2: " . $rx["static"][$p[1] . '/' . $p[2]]);
                    $this->uri = $rx["static"][$p[1] . '/' . $p[2]];
                    $matchflag++;
                }
            } elseif ($co == 2) {
                if (isset($rx["static"][$p[1]])) {
                    // die("static1: " . $rx["static"][$p[1]]);
                    $this->uri = $rx["static"][$p[1]];
                    $matchflag++;
                }
            }

            if ($matchflag < 1) {
                // Auto route
                $this->uri = $uri;
            }

            //echo $uri;
            //var_dump($this->uri);

            $parts = explode("/", $this->uri);

            // Validate all parts for auto route are available
            for ($i = 1; $i < 4; $i++) {
                if (!isset($parts[$i]) || $parts[$i] == NULL) {
                    // die("404! That route was not found.");
                    show404("404! That route was not found.");
                }
            }

            $this->parts = [
                $parts[1],
                $parts[2],
                $parts[3],
            ];

            // Parse Module | Controller | Method for this request
            $this->autoroute();
        }
    }

    private function autoroute()
    {
        $parts = $this->parts;
        // var_dump($parts); die;

        if (isset($parts)) {

            if (count($parts) > 2) {
                $this->module       = ucfirst($parts[0]);
                $this->controller   = ucfirst($parts[1]);
                $this->method       = $parts[2];
            } else {
                header("Location:" . BASE_URL . "/home");
                die;
                //die("Incomplete routing info...");
            }
        } else {
            die("404 - Routing info missing...");
        }
    }
}
