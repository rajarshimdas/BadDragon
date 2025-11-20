<?php /*
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On: 19-Feb-2024                               |
| Updated On: 23-Oct-2025 ChatGPT                       |
+-------------------------------------------------------+
*/
define('BADDRAGON', 'Ver 2.0');

$classmap = [
    'BadDragon' => __DIR__ . '/Engine',
];

spl_autoload_register(function (string $classname) use ($classmap) {

    $parts = explode('\\', $classname);
    $namespace = array_shift($parts);
    $classfile = array_pop($parts) . '.php';

    if (!array_key_exists($namespace, $classmap)) {
        return;
    }

    // Build path safely
    $path = $parts ? DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) : '';
    $file = $classmap[$namespace] . $path . DIRECTORY_SEPARATOR . $classfile;

    if (file_exists($file)) {
        require_once $file;
    }
});
