<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event\player\skill\combat;

use cosmicpe\mcmmo\event\player\skill\McMMOPlayerSkillEvent;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\Skill;
use pocketmine\event\entity\EntityDamageEvent;

final class McMMOPlayerRollEvent extends McMMOPlayerSkillEvent{

	private EntityDamageEvent $cause;

	public function __construct(McMMOPlayer $player, Skill $skill, EntityDamageEvent $cause){
		parent::__construct($player, $skill);
		$this->cause = $cause;
	}

	public function getCause() : EntityDamageEvent{
		return $this->cause;
	}
}