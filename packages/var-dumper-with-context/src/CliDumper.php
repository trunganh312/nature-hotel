<?php

namespace Packages\VarDumperWithContext;

use Symfony\Component\VarDumper\Dumper\CliDumper as BaseDumper;

class CliDumper extends BaseDumper implements DumpContextInterface {

	use ContextDumper;

	public function getContext($file, $line) {
		return "\n{$file}:{$line}";
	}

}
