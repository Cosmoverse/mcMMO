<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event\player\skill;

use cosmicpe\mcmmo\event\player\McMMOPlayerEvent;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\Skill;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class McMMOPlayerSkillEvent extends McMMOPlayerEvent implements Cancellable{
	use CancellableTrait;

	protected Skill $skill;

	public function __construct(McMMOPlayer $player, Skill $skill){
		parent::__construct($player);
		$this->skill = $skill;
	}

	public function getSkill() : Skill{
		return $this->skill;
	}
}