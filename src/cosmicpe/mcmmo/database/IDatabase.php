<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\database;

use Closure;
use cosmicpe\mcmmo\player\McMMOPlayer;
use Ramsey\Uuid\UuidInterface;

interface IDatabase{

	/**
	 * @param UuidInterface $uuid
	 * @param Closure $callback
	 */
	public function load(UuidInterface $uuid, Closure $callback) : void;

	public function save(McMMOPlayer $player) : void;

	public function close() : void;
}