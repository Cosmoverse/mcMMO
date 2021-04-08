<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use cosmicpe\mcmmo\player\McMMOPlayer;
use pocketmine\player\Player;

final class McMMOListenerParserResult{

	public Player $player;
	public McMMOPlayer $mcmmo_player;

	public function __construct(Player $player, McMMOPlayer $mcmmo_player){
		$this->player = $player;
		$this->mcmmo_player = $mcmmo_player;
	}
}