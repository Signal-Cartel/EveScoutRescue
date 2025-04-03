<?php
/**
 * Page(s) that include this page:
 * - included on every page that needs to load a class
 * 
 * Requirements:
 * - None
 * 
 * jmh - 20200716
 */


// page cannot be accessed directly
if (!defined('ESRC')) { die ('Direct access not permitted'); }

// this will autoload any class by name when it is instantiated
// class files must be in the 'class' directory in order to be loaded
spl_autoload_register(function ($className) {
    $path = sprintf('%1$s%2$s%3$s.php',
        // %1$s: get absolute path
        realpath(dirname(__FILE__)),
        // %2$s: / or \ (depending on OS)
        DIRECTORY_SEPARATOR,
        // %3$s: replace \ by / or \ (depending on OS)
        str_replace('\\', DIRECTORY_SEPARATOR, strtolower($className).'.class')
    );

    if (file_exists($path)) {
        include $path;
		//file_put_contents('debug_log.txt', "Resolved Path: " . $path . " for class: " . $className . PHP_EOL, FILE_APPEND);

    } else {
        throw new Exception(
            sprintf('Class with name %1$s not found. Looked in %2$s',
                $className,
                $path
            )
        );
    }
});
?>