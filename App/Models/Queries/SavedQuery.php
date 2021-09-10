<?php

namespace App\Models\Queries;

use Exception;

class SavedQuery
{
	public string $slug;
	public function __construct($params)
	{
		$this->slug = $params['slug'] ?? throw new Exception();
	}
}
