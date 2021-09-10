<?php

namespace Core\Database;

class PDOPlus
{

	public \PDO $conn;

	private array $connectOptions = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
	];

	private string $placeholder = "?";

	public function __construct(string $server, string $database, string $username, string $password, array $options = [], string $databaseType = "mysql")
	{
		$this->connectOptions = array_replace($this->connectOptions, $options);
		$this->conn = new \PDO("$databaseType:host=$server;dbname=$database", $username, $password, $this->connectOptions);
	}

	public function getConn(): \PDO
	{
		return $this->conn;
	}

	public function query(string $query, mixed $values = [])
	{

		$values = (array) $values;

		if (!is_array($values)) {
			$values = json_decode(json_encode($values), true);
		}

		$preparedQuery = $this->prepareQuery($query, $values);
		$query = $preparedQuery['query'];
		$values = $preparedQuery['values'];

		$conn = $this->conn;

		$stmt = $conn->prepare($query);
		$results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		return $results;
		return $this->detectAndParseInt($results);
	}

	private function prepareQuery(string $query, array $values): array
	{

		if (substr_count($query, $this->placeholder) !== count($values)) {
			throw new \PDOException("Number of placeholders does not match values provided.");
		}

		$placeholders = [];
		$flattenedValues = [];

		foreach ($values as $value) {
			$depth = $this->getDepth($value);

			if ($depth === 0) {
				$placeholders[] = "?";
				$flattenedValues[] = $value;
			} elseif ($depth === 1 && !empty($value)) {
				$placeholders[] = implode(",", array_fill(0, count($value), "?"));
				array_push($flattenedValues, ...$value);
			} elseif ($depth === 2 && !empty($value)) {
				$tempPlaceholders = [];
				foreach ($value as $row) {
					if (!empty($row)) {
						$tempPlaceholders[] = "(" . implode(",", array_fill(0, count($row), "?")) . ")";
						foreach ($row as $field) {
							$flattenedValues[] = $field;
						}
					} else {
						throw new \PDOException("Invalid data structure.");
					}
				}
				$placeholders[] = implode(",", $tempPlaceholders);
			} else {
				throw new \PDOException("Invalid data structure.");
			}
		}
		$query = $this->injectPlaceholders($query, $placeholders);

		if (substr_count($query, "?") !== count($flattenedValues)) {
			throw new \PDOException("Error compiling placeholders.");
		}

		return [
			'query' => $query,
			'values' => $flattenedValues,
		];
	}

	/**
	 * Returns depth of variable
	 */
	private function getDepth(mixed $value, int $depth = 0): int
	{
		if (is_array($value)) {
			$depth++;
			$nextLevel = array_key_first($value);
			if ($nextLevel !== null) {
				$depth = $this->getDepth($value[$nextLevel], $depth);
			}
		}
		return $depth;
	}

	/**
	 * Inject new placeholders into query string from array.
	 */
	private function injectPlaceholders(string $query, array $placeholders): string
	{
		$curIndex = 0;
		return preg_replace_callback(
			"/" . preg_quote($this->placeholder) . "/",
			function () use (&$curIndex, &$placeholders) {
				return $placeholders[$curIndex++] ?? null;
			},
			$query
		);
	}

	/**
	 * Converts field to integer only if all rows can be. 
	 */
	private function detectAndParseInt(array $values): array
	{
		$fieldTypes = [];
		foreach ($values as $row) {
			foreach ($row as $field => $value) {
				$fieldTypes[$field][] = is_numeric($value);
			}
		}
		foreach ($fieldTypes as &$field) {
			$field = !in_array(false, $field);
		}
		foreach ($values as &$row) {
			foreach ($row as $field => &$value) {
				if ($fieldTypes[$field]) {
					$value = (int) $value;
				}
			}
		}
		return $values;
	}
}
