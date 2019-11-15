<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event\player;

use cosmicpe\mcmmo\event\McMMOEvent;
use cosmicpe\mcmmo\player\McMMOPlayer;

abstract class McMMOPlayerEvent extends McMMOEvent{

	/** @var McMMOPlayer */
	protected $player;

	public function __construct(McMMOPlayer $player){
		$this->player = $player;
	}

	public function getMcMMOPlayer() : McMMOPlayer{
		return $this->player;
	}
}