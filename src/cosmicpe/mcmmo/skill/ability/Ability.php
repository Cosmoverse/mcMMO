<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\ability;

use cosmicpe\mcmmo\player\McMMOPlayer;
use pocketmine\item\Item;
use pocketmine\player\Player;

interface Ability{

	public function getToolType() : int;

	public function getSubSkillIdentifier() : string;

	public function getDuration(Player $player, McMMOPlayer $mcmmo_player, Item $item) : int;

	public function onAdd(Player $player, McMMOPlayer $mcmmo_player, Item $item) : void;
}