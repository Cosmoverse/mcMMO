<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event\player;

use cosmicpe\mcmmo\event\McMMOEvent;
use cosmicpe\mcmmo\player\McMMOPlayer;

abstract class McMMOPlayerEvent extends McMMOEvent{

	public function __construct(
		protected McMMOPlayer $player
	){}

	public function getMcMMOPlayer() : McMMOPlayer{
		return $this->player;
	}
}