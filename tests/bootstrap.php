<?php

require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function (string $class) {
    $shortClass = (strpos($class, 'App\\') === 0) ? substr($class, 4) : $class;

    $candidates = [
        $shortClass,
        str_replace('Validation\\', 'Fonctions\\', $shortClass),
        basename(str_replace('\\', '/', $shortClass))
    ];

    // Si la classe réelle est déjà en mémoire, on crée simplement l'alias App\...
    foreach ($candidates as $candidate) {
        if (class_exists($candidate, false) || interface_exists($candidate, false)) {
            if ($candidate !== $class && !class_exists($class, false)) {
                class_alias($candidate, $class);
            }
            return;
        }
    }

    // Sinon on charge le fichier en normalisant son chemin avec realpath()
    $paths = [
        __DIR__ . '/../' . str_replace('\\', '/', $shortClass) . '.php',
        __DIR__ . '/../' . str_replace('\\', '/', str_replace('Validation\\', 'Fonctions\\', $shortClass)) . '.php',
    ];

    foreach ($paths as $file) {
        $realFile = realpath($file);
        if ($realFile && is_file($realFile)) {
            require_once $realFile;
            foreach ($candidates as $candidate) {
                if ((class_exists($candidate, false) || interface_exists($candidate, false)) && $candidate !== $class && !class_exists($class, false)) {
                    class_alias($candidate, $class);
                    return;
                }
            }
        }
    }
}, true, true); // true en 3ème argument place cet autoloader AVANT celui de Composer