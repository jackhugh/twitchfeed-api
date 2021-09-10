<?php

namespace App\Models;

class ClipIndexModel
{
	public function __construct(
		public int $broadcaster_id,
		public string $broadcaster_name,
		public int $game_id,
		public string $game_name,
	) {
	}
}
