<?php

namespace App\Providers\Twitch;

interface TwitchScope
{
	const ANALYTICS_READ_EXTENSIONS = "analytics:read:extensions";
	const ANALYTICS_READ_GAMES = "analytics:read:games";
	const BITS_READ = "bits:read";
	const CHANNEL_EDIT_COMMERCIAL = "channel:edit:commercial";
	const CHANNEL_MANAGE_BROADCAST = "channel:manage:broadcast";
	const CHANNEL_MANAGE_EXTENSIONS = "channel:manage:extensions";
	const CHANNEL_MANAGE_REDEMPTIONS = "channel:manage:redemptions";
	const CHANNEL_READ_HYPE_TRAIN = "channel:read:hype_train";
	const CHANNEL_READ_REDEMPTIONS = "channel:read:redemptions";
	const CHANNEL_READ_STREAM_KEY = "channel:read:stream_key";
	const CLIPS_EDIT = "clips:edit";
	const MODERATION_READ = "moderation:read";
	const USER_EDIT = "user:edit";
	const USER_EDIT_FOLLOWS = "user:edit:follows";
	const USER_READ_BROADCAST = "user:read:broadcast";
	const USER_READ_EMAIL = "user:read:email";
}
