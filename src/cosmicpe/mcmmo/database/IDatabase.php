<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\database;

use Closure;
use cosmicpe\mcmmo\player\Player;
use pocketmine\utils\UUID;

interface IDatabase{

	/**
	 * @param UUID $uuid
	 * @param Closure<Player> $callback
	 */
	public function load(UUID $uuid, Closure $callback) : void;

	public function save(Player $player) : void;

	public function close() : void;
}