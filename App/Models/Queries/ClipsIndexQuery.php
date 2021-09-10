<?php

namespace App\Models\Queries;

use Exception;

class ClipsIndexQuery
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

	public function __construct(
		string $sort,
	) {

		$this->sort = self::SORTS[$sort]['sort'] ?? throw new Exception();
		$this->period = self::SORTS[$sort]['period'] ?? throw new Exception();
	}
}
