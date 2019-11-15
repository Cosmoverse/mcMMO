<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\customitem\listener;

use pocketmine\item\Item;
use pocketmine\player\Player;

interface Movable{

	/**
	 * Returns whether to stop the movement from happening.
	 *
	 * @param Player $player
	 * @param Item $item
	 * @return bool
	 */
	public function onMoveItem(Player $player, Item $item) : bool;
}