<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event\player\skill;

use cosmicpe\mcmmo\event\player\McMMOPlayerEvent;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\Skill;

abstract class McMMOPlayerSkillEvent extends McMMOPlayerEvent{

	/** @var Skill */
	protected $skill;

	public function __construct(McMMOPlayer $player, Skill $skill){
		parent::__construct($player);
		$this->skill = $skill;
	}

	public function getSkill() : Skill{
		return $this->skill;
	}
}