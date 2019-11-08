<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use pocketmine\event\Listener;

interface Listenable{

	/**
	 * @return string[]|Listener[]
	 */
	public function getListeners() : array;
}