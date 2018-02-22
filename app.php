<?php
/**
* @author  <>
*/
/**
 * Usage php __FILE__ {directory} {authorName} {authorEmail}
 */

$rootDir = isset($argv[1]) && is_dir($argv[1]) ? $argv[1] : null;
$author  = isset($argv[2]) ? $argv[2] : null;
$email   = isset($argv[3]) ? $argv[3] : null;

$rootDir = __DIR__;

$phpOpenTag = '<?php';

function getFileExtension($fileName) {
    return substr(strrchr($fileName, '.'), 1);
}

/**
 * @param string $author
 * @param string $email
 * @return string
 */
function getAnnotation($author, $email) {
    return "\n/**\n* @author {$author} <{$email}>\n*/";
}

function getPhpClassesRecursive($dir) {
    $result = [];
    global $phpOpenTag;
    $contents = array_filter(scandir($dir), function ($content) {
        return !in_array($content, ['.', '..']);
    });
    foreach ($contents as $content) {
        if (is_dir($subDirectory = "{$dir}/{$content}")) {
            $result = array_merge($result, getPhpClassesRecursive($subDirectory));
            continue;
        }
        if (getFileExtension($content) === 'php' && (strpos(file_get_contents($phpClass = $dir . '/' . $content), $phpOpenTag) === 0)) {
            $result[] = $phpClass;
        }
    }
    return $result;
}

function applyAnnotation(array $phpClasses, $annotation) {
    global $phpOpenTag;
    foreach ($phpClasses as $class) {
        $contents = file_get_contents($class);
        $firstPart = substr($contents, 0, strlen($phpOpenTag));
        $lastPart = substr($contents, strlen($phpOpenTag));
        $contents = $firstPart . $annotation . $lastPart;
        $fileStream = fopen($class, 'w');
        fwrite($fileStream, $contents);
        fclose($fileStream);
    }
}

$phpClasses = getPhpClassesRecursive($rootDir);
applyAnnotation($phpClasses, getAnnotation($author, $email));