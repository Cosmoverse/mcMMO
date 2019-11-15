<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\customitem\listener;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\player\Player;

interface Interactable{

	/**
	 * Returns whether this item's state was updated.
	 *
	 * @param Player $player
	 * @param Item $item
	 * @param Block $block
	 * @return bool
	 */
	public function onInteract(Player $player, Item $item, Block $block) : bool;
}