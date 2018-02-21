<?php

$rootDir = isset($argv[1]) && is_dir($argv[1]) ? $argv[1] : null;

$rootDir = __DIR__ . '/test';

function getFileExtension($fileName) {
    return substr(strrchr($fileName, '.'), 1);
}

/**
 * @param string $author
 * @param string $email
 * @return string
 */
function getAnnotation($author, $email) {
    return "/**\n* @author {$author} <{$email}>\n*/";
}

function getPhpClassesRecursive($dir) {
    $result = [];
    $contents = array_filter(scandir($dir), function ($content) {
        return !in_array($content, ['.', '..']);
    });
    foreach ($contents as $content) {
        if (is_dir($subDirectory = "{$dir}/{$content}")) {
            $result = array_merge($result, getPhpClassesRecursive($subDirectory));
            continue;
        }
        if (getFileExtension($content) === 'php') {
            $result[$dir] = $content;
        }
    }
    return $result;
}

function applyAnnotation(array $phpClasses, $annotation) {
    foreach ($phpClasses as $path => $class) {
        if (strpos($contents = file_get_contents($path . '/' . $class), '<?php') !== 0) {
            continue;
        }
        $contents = strtok($contents, '\n');
        while (is_string($contents)) {
            echo $contents;
            $contents = strtok('\n');
        }
    }
}

$phpClasses = getPhpClassesRecursive($rootDir);
applyAnnotation($phpClasses, getAnnotation('serqol', 'serqol@mail.ru'));