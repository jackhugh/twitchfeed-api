<?php

namespace App\Models;

class ClipModel
{
	public function __construct(
		public string $title,
		public string $slug,
		public int $view_count,
		public int $created_at,
		public string $thumbnail_url,
		public int $broadcaster_id,
		public string $broadcaster_name,
		public int $game_id,
		public string $game_name,
		public bool $saved,
	) {
	}
}
