<?php

namespace Packages\VarDumperWithContext;

use src\Libs\Utils;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

VarDumper::setHandler(function ($var) {
    $cloner = new VarCloner();
    $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper();
    if (Utils::isPro() && Utils::isDemo()) {
        $dumper->saveLog((string) $cloner->cloneVar($var));
    } else {
        $dumper->dump($cloner->cloneVar($var));
    }
});