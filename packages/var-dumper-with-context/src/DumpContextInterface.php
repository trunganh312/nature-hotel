<?php

namespace Packages\VarDumperWithContext;

interface DumpContextInterface {

	public function getContext($file, $line);

}