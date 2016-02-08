<?php
/*
 * This file is part of the xpdo.org package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use xPDO\xPDO;

$properties = [];

array_shift($argv);
$command = array_shift($argv);

$arg = function($idx = 1) use ($argv) {
    $current = 1;
    foreach ($argv as $arg) {
        if (preg_match('{^-}', $arg)) continue;
        if ($current === $idx) return $arg;
        $current++;
    }
    return null;
};

$opt = function($find) use ($argv) {
    $value = false;
    $findPrefix = strlen($find) === 1 ? '-' : '--';
    $re = '{^' . $findPrefix . '(' . $find . ')=?(.*)?}';
    $matches = array();
    foreach ($argv as $opt) {
        if (preg_match($re, $opt, $matches)) {
            $value = true;
            if ($matches[2] !== '') {
                $value = $matches[2];
            }
            break;
        }
    }
    return $value;
};

$platforms = array('mysql', 'sqlite', 'sqlsrv');

$verbose = $opt('verbose') || $opt('v');

$config = $opt('config');
if (empty($config) || !is_readable($config)) {
    $config = dirname(__DIR__) . '/config.php';
}
$properties = require $config;
if (!is_array($properties)) {
    echo "fatal: no valid configuration file specified" . PHP_EOL;
    exit(128);
}
if ($verbose) {
    echo "using config from {$config}" . PHP_EOL;
}

switch ($command) {
    case 'init':
        $platform = $arg(1);
        if ($platform === null || !in_array(strtolower($platform), $platforms)) {
            echo "fatal: no valid platform specified" . PHP_EOL;
            exit(128);
        }
        $platform = strtolower($platform);

        $xpdo = xPDO::getInstance('generator', $properties["{$platform}_array_options"]);
        $xpdo->setLogLevel(xPDO::LOG_LEVEL_INFO);
        $xpdo->setLogTarget(PHP_SAPI === 'cli' ? 'ECHO' : 'HTML');

        $xpdo->getManager()->removeObjectContainer('xPDO\DotOrg\Releases\Release');

        $xpdo->getManager()->createObjectContainer('xPDO\DotOrg\Releases\Release');

        $releases = require __DIR__ . '/releases.php';

        foreach ($releases as $release) {
            $xpdo->newObject('xPDO\DotOrg\Releases\Release', $release)->save();
        }
        exit(0);
        break;
    default:
        echo "unknown command {$command}" . PHP_EOL;
        break;
}

echo <<<'EOF'
Example usage:
  xpdo init [--verbose|-v] [[--config|-C]=CONFIG/FILE] PLATFORM

EOF;
exit(0);
