<?php /*
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On:   19-Feb-2024                             |
| Updated On:                                           |
+-------------------------------------------------------+
*/
define('BADDRAGON', 'Ver 1.0.0');

$classmap = [
    'BadDragon' => __DIR__ . '/App',
];

spl_autoload_register(function (string $classname) use ($classmap) {

    $parts = explode('\\', $classname);
    // var_dump($parts);
    $namespace = array_shift($parts);
    $classfile = array_pop($parts) . '.php';

    if (!array_key_exists($namespace, $classmap)) {
        return;
    }

    $path = implode(DIRECTORY_SEPARATOR, $parts);
    $file = $classmap[$namespace] . $path . DIRECTORY_SEPARATOR . $classfile;

    if (!file_exists($file) && !class_exists($classname)) {
        return;
    }

    require_once $file;
});
