<?php

namespace App\Models\Queries;

use Exception;

class ClipsSavedQuery
{

	public const SORTS = [
		"top",
		"new",
		"added"
	];

	public string $sort;
	public int $next;
	public array $game;
	public array $channel;

	public function __construct(
		string $sort,
		int $next = 0,
		array $game = [],
		array $channel = [],
	) {

		$this->sort = in_array($sort, self::SORTS) ? $sort : throw new Exception();
		$this->next = $next;
		$this->game = $game;
		$this->channel = $channel;
	}
}
