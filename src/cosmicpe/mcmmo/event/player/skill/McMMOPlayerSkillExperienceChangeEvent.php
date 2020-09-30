<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event\player\skill;

use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\experience\SkillExperienceManager;
use cosmicpe\mcmmo\skill\Skill;

class McMMOPlayerSkillExperienceChangeEvent extends McMMOPlayerSkillEvent{

	public const CAUSE_SKILL = 0;
	public const CAUSE_CUSTOM = 1;

	/** @var int */
	protected $old_experience;

	/** @var int */
	protected $new_experience;

	/** @var int */
	protected $old_level;

	/** @var int */
	protected $cause;

	public function __construct(McMMOPlayer $player, Skill $skill, int $old_experience, int $new_experience, int $cause){
		parent::__construct($player, $skill);
		$this->old_experience = $old_experience;
		$this->new_experience = $new_experience;
		$this->old_level = SkillExperienceManager::get()->getLevelFromExperience($this->old_experience);
		$this->cause = $cause;
	}

	public function getOldExperience() : int{
		return $this->old_experience;
	}

	public function getNewExperience() : int{
		return $this->new_experience;
	}

	public function getOldLevel() : int{
		return $this->old_level;
	}

	public function getNewLevel() : int{
		return SkillExperienceManager::get()->getLevelFromExperience($this->new_experience);
	}

	public function setNewExperience(int $value) : void{
		$this->new_experience = $value;
	}

	public function getCause() : int{
		return $this->cause;
	}
}