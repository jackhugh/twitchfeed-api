<?php
session_start([
	'save_path' => __DIR__ . "/../../sess",
	'gc_probability' => 1,
	'gc_divisor' => 1,
	'gc_maxlifetime' => APP_COOKIE_LIFETIME,
]);
session_destroy();
