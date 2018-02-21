<?php

$rootDir = isset($argv[1]) && is_dir($argv[1]) ? $argv[1] : null;

$rootDir = __DIR__ . '/test';

function getFileExtension($fileName) {
    return substr(strrchr($fileName, '.'), 1);
}

function getPhpClassesRecursive($dir) {
    $result = [];
    $contents = array_filter(scandir($dir), function ($content) {
        return !in_array($content, ['.', '..']);
    });
    foreach ($contents as $content) {
        if (is_dir($subDirectory = "{$dir}/{$content}")) {
            $result[$subDirectory] = getPhpClassesRecursive($subDirectory);
            continue;
        }
        if (getFileExtension($content) === 'php') {
            $result[$dir] = $content;
        }
    }
    return $result;
}

function filterPhpClasses(array $phpClasses) {
    $result = [];
    foreach ($phpClasses as $path => $class) {
        $contents = (string)file_get_contents("{$path}/{$class}");
        if (strpos($contents, '<?php') !== 0) {
            continue;
        }
        $result[] = $contents;
    }
    return $result;
}

$phpClasses = getPhpClassesRecursive($rootDir);
$filteredPhpClasses = filterPhpClasses($phpClasses);

var_dump($filteredPhpClasses);