<?php

namespace App\Models\Queries;

use Exception;

class ClipsFollowingQuery
{

	public const SORTS = [
		"top-all" => [
			'period' => 'ALL',
			'sort' => 'top',
		],
		"top-month" => [
			'period' => 'MONTH',
			'sort' => 'top',
		],
		"top-week" => [
			'period' => 'WEEK',
			'sort' => 'top',
		],
		"top-day" => [
			'period' => 'DAY',
			'sort' => 'top',
		],
		"hot" => [
			'period' => 'DAY',
			'sort' => 'hot',
		],
		"new" => [
			'period' => 'DAY',
			'sort' => 'new',
		],
	];

	public string $sort;
	public string $period;
	public int $next;
	public array $game;
	public array $channel;

	public function __construct(
		string $sort,
		int $next = 0,
		string $game = "",
		string $channel = "",
	) {

		$this->sort = self::SORTS[$sort]['sort'] ?? throw new Exception();
		$this->period = self::SORTS[$sort]['period'] ?? throw new Exception();
		$this->next = $next;
		$this->game = !empty($game) ? explode(",", $game) : [];
		$this->channel = !empty($channel) ? explode(",", $channel) : [];
	}
}
