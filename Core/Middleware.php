<?php

namespace Core;

interface Middleware
{
	public function run(Request $req, Response $resp);
}
