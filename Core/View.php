<?php

namespace Core;

class View
{

	/**
	 * Returns pure HTML with values injected and sanetized.
	 * 
	 * @param string $file File relative to App/views.
	 * @param array $params Values to be injected indexed by variable name.
	 * @param bool $sanetize Whether or not to sanetize values (using 'htmlentities()') , default is true.
	 * 
	 * @return string
	 */
	public static function render(string $file, array $params = [], bool $sanetize = true): string
	{
		extract($params);
		ob_start();
		require __DIR__ . "/../App/Views/" . $file;
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	public static function sanetize(&$data)
	{
		if (!is_array($data)) $data = [$data];

		array_walk_recursive($data, function (&$value) {
			$value = htmlentities($value);
		});

		return $data;
	}
}
