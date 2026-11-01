<?php
ob_start();
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require_once __DIR__ . '/../vendor/autoload.php';

// Charger les fonctions globales (generate, logError, etc.) si elles ne sont pas encore incluses
foreach (glob(__DIR__ . '/../Fonctions/*.php') as $funcFile) {
    require_once realpath($funcFile);
}

spl_autoload_register(function (string $class) {
    // On ignore toutes les classes externes (PHPUnit, Sebastian, Doctrine, etc.)
    $projectPrefixes = ['App\\', 'Controllers\\', 'Models\\', 'Session\\', 'Database\\', 'Fonctions\\', 'Validation\\'];
    $isProjectClass = false;
    foreach ($projectPrefixes as $prefix) {
        if (strpos($class, $prefix) === 0) {
            $isProjectClass = true;
            break;
        }
    }
    if (!$isProjectClass) {
        return;
    }

    $shortClass = (strpos($class, 'App\\') === 0) ? substr($class, 4) : $class;

    $candidates = [
        $shortClass,
        str_replace('Validation\\', 'Fonctions\\', $shortClass),
    ];

    // 1. Si la classe cible est déjà chargée en mémoire, on crée l'alias App\...
    foreach ($candidates as $candidate) {
        if (class_exists($candidate, false) || interface_exists($candidate, false)) {
            if ($candidate !== $class && !class_exists($class, false)) {
                class_alias($candidate, $class);
            }
            return;
        }
    }

    // 2. Sinon on charge le fichier une seule fois avec realpath()
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
});

// Alias global pour les mocks appelés sans namespace (ex: TexteControllerTest)
if (!class_exists('TexteRepository') && class_exists('Models\\TexteRepository')) {
    class_alias('Models\\TexteRepository', 'TexteRepository');
}
