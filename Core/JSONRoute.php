<?php

namespace Core;

class JSONRoute extends Route
{
	public function __construct(...$args)
	{
		parent::__construct(...$args);
		$this->type = 'JSON';
	}
}
