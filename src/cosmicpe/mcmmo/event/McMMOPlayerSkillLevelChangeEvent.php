<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event;

use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\experience\SkillExperienceManager;
use cosmicpe\mcmmo\skill\Skill;

class McMMOPlayerSkillLevelChangeEvent extends McMMOPlayerSkillExperienceChangeEvent{

	/** @var int */
	protected $old_level;

	public function __construct(McMMOPlayer $player, Skill $skill, int $old_experience, int $new_experience, int $old_level){
		parent::__construct($player, $skill, $old_experience, $new_experience);
		$this->old_level = $old_level;
	}

	public function getOldLevel() : int{
		return $this->old_level;
	}

	public function getNewLevel() : int{
		return SkillExperienceManager::get()->getLevelFromExperience($this->new_experience);
	}
}