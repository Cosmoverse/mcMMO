<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use cosmicpe\mcmmo\player\McMMOPlayer;
use pocketmine\player\Player;

final class McMMOListenerParserResult{

	public function __construct(
		public Player $player,
		public McMMOPlayer $mcmmo_player
	){}
}