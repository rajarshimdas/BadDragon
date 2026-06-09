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
    }

    /**
     * Load controller files (module, controller, and script)
     * for the given route.
     *
     * @param object $route
     * @return array|null
     */
    public function fire(object $route): array
    {
        // Define file paths for module, controller, and script
        $files = [
            'Module'     => W3APP . '/Controller/' . $route->module . '/' . $route->module . '.php',
            'Controller' => W3APP . '/Controller/' . $route->module . '/' . $route->controller . '/' . $route->controller . '.php',
            'Script'     => W3APP . '/Controller/' . $route->module . '/' . $route->controller . '/' . $route->method . '.php',
        ];

        // Validate all files in one pass
        foreach ($files as $type => $filepath) {
            if (!is_file($filepath)) {
                $this->handleMissing($type, $filepath);
            }
        }

        // Return file stack in execution order
        return array_values($files);
    }

    /**
     * Handles missing files with proper error messages.
     */
    private function handleMissing(string $type, string $file): null
    {
        $msg = sprintf("Error[BD::Controller]: %s file missing: %s", $type, $file);

        if (defined('ENV') && ENV === 'dev') {
            die($msg);
        }

        if (function_exists('show404')) {
            show404("404: $msg");
        }
        
        die("404: $msg");
    }
}
