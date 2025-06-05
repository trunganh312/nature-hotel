<?php

namespace Packages\VarDumperWithContext;

use Symfony\Component\VarDumper\Cloner\Data;

trait ContextDumper
{
    /**
     * Finds the first file NOT in the /vendor/ directory from the
     * backtrace.
     */
    protected function getCaller()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $routes = [];
        foreach ($backtrace as $trace) {
            if (!empty($trace['file']) &&
                !empty($trace['line']) && 
                strpos($trace['file'], '/vendor/') === false  &&
                strpos($trace['file'], '/packages/') === false
            ) {
                $routes[] = $trace;
            }
        }
        return $routes;
    }

    public function dump(Data $data, $output = null, array $extraDisplayOptions = array()) : string
    {
        $callers = $this->getCaller();
        if (!$callers) return parent::dump($data, $output);

        foreach ($callers as $stt => $caller) {
            $caller['file'] = ($stt+1) .'. function ['. $caller['function'] .'] in '. $caller['file'];
            $line = $this->getContext($caller['file'], $caller['line']);
            parent::echoLine($line, 0, '');
        }
        parent::dump($data, $output);
        return '';
    }

    public function saveLog($content) {
        $result = $this->getCaller();
        $result["msg"] = $content;
        save_log("dd_production.log", print_r($result, true));
        exit;
    }
}
