<?php

namespace App\Providers;

use Core\Database\Database;

class QueryBuilder
{

	private string $selectTable;
	private array $selectFields;
	private bool $distinct = false;
	private array $joins = [];
	private array $where = [];
	private array $groupBy = [];
	private array $orderBy = [];
	private int $limit;
	private int $offset;

	private array $values = [];

	public static function SELECT(string $table)
	{
		return new self(...func_get_args());
	}

	public static function minAge(string $value, string $period)
	{
		return "{$value} < DATE_SUB(NOW(), INTERVAL {$period})";
	}

	public static function maxAge(string $value, string $period)
	{
		return "{$value} > DATE_SUB(NOW(), INTERVAL {$period})";
	}

	public function __toString()
	{
		$query = "";

		$add = function ($addStr) use (&$query) {
			$query .= $addStr . "\n";
		};

		$add("SELECT" . ($this->distinct ? " DISTINCT" : ""));
		$add(implode(",\n", array_map(fn ($elem) => "    {$elem}", $this->selectFields)));
		$add("FROM {$this->selectTable}");

		$add(implode("\n", $this->joins));

		if (!empty($this->where)) {
			$add("WHERE 1=1");
			$add(implode("\n", array_map(fn ($elem) => "    AND {$elem}", $this->where)));
		}

		if (!empty($this->groupBy)) {
			$add("GROUP BY");
			$add(implode(",\n", array_map(fn ($elem) => "    {$elem}", $this->groupBy)));
		}

		if (!empty($this->orderBy)) {
			$add("ORDER BY");
			$add(implode(",\n", array_map(fn ($elem) => "    {$elem}", $this->orderBy)));
		}

		if (isset($this->limit)) {
			$add("LIMIT {$this->limit}");
		}

		if (isset($this->offset)) {
			$add("OFFSET {$this->offset}");
		}
		return $query;
	}

	public function send()
	{
		$db = Database::get();
		return $db->query($this->__toString(), $this->values);
	}

	public function __construct(string $table)
	{
		$this->selectTable  = $table;
	}

	public function field(string $field)
	{
		$this->selectFields[] = "{$this->selectTable}.{$field}";
		return $this;
	}

	public function customField(string $field)
	{
		$this->selectFields[] = $field;
		return $this;
	}

	public function distinct(bool $isDistinct)
	{
		$this->distinct = $isDistinct;
		return $this;
	}

	public function innerJoin(string $table, string $thisKey, string $foreignKey)
	{
		$this->joins[] = "INNER JOIN {$table} ON {$this->selectTable}.{$thisKey} = {$table}.{$foreignKey}";
		return $this;
	}

	public function leftJoin(string $table, string $thisKey, string $foreignKey)
	{
		$this->joins[] = "LEFT JOIN {$table} ON {$this->selectTable}.{$thisKey} = {$table}.{$foreignKey}";
		return $this;
	}

	public function where(string $whereQuery, $bindValue = null)
	{
		if (!is_null($bindValue)) $this->values[] = $bindValue;

		$this->where[] = $whereQuery;
		return $this;
	}

	public function groupBy(string $grouping)
	{
		$this->groupBy[] = $grouping;
		return $this;
	}

	public function orderBy(string $orderByQuery)
	{
		$this->orderBy[] = $orderByQuery;
		return $this;
	}

	public function limit(int $limit)
	{
		$this->limit = $limit;
		return $this;
	}

	public function offset(int $offset)
	{
		$this->offset = $offset;
		return $this;
	}


	public function fieldIf(bool $condition, string $field)
	{
		if ($condition) $this->field(...array_slice(func_get_args(), 1));
		return $this;
	}

	public function customFieldIf(bool $condition, string $field)
	{
		if ($condition) $this->customField(...array_slice(func_get_args(), 1));
		return $this;
	}

	public function innerJoinIf(bool $condition, string $table, string $thisKey, string $foreignKey)
	{
		if ($condition) $this->innerJoin(...array_slice(func_get_args(), 1));
		return $this;
	}

	public function leftJoinIf(bool $condition, string $table, string $thisKey, string $foreignKey)
	{
		if ($condition) $this->leftJoin(...array_slice(func_get_args(), 1));
		return $this;
	}

	public function whereIf(bool $condition, $bindValue = null)
	{
		if ($condition) $this->where(...array_slice(func_get_args(), 1));
		return $this;
	}

	public function groupByIf(bool $condition, string $grouping)
	{
		if ($condition) $this->groupBy(...array_slice(func_get_args(), 1));
		return $this;
	}

	public function orderByIf(bool $condition, string $orderByQuery)
	{
		if ($condition) $this->orderBy(...array_slice(func_get_args(), 1));
		return $this;
	}

	public function limitIf(bool $condition, int $limit)
	{
		if ($condition) $this->limit(...array_slice(func_get_args(), 1));
		return $this;
	}

	public function offsetIf(bool $condition, int $offset)
	{
		if ($condition) $this->offset(...array_slice(func_get_args(), 1));
		return $this;
	}
}
