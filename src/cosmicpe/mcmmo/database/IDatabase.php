<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\database;

use Closure;
use cosmicpe\mcmmo\player\McMMOPlayer;
use pocketmine\uuid\UUID;

interface IDatabase{

	/**
	 * @param UUID $uuid
	 * @param Closure $callback
	 */
	public function load(UUID $uuid, Closure $callback) : void;

	public function save(McMMOPlayer $player) : void;

	public function close() : void;
}