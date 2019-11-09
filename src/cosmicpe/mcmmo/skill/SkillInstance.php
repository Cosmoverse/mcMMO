<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use cosmicpe\mcmmo\skill\experience\SkillExperienceInstance;

final class SkillInstance{

	/** @var Skill */
	private $skill;

	/** @var int|null */
	private $cooldown;

	/** @var SkillExperienceInstance */
	private $experience;

	public function __construct(Skill $skill, ?int $cooldown = null, int $experience = 0){
		$this->skill = $skill;
		$this->cooldown = $cooldown;
		$this->experience = new SkillExperienceInstance($experience);
	}

	public function getSkill() : Skill{
		return $this->skill;
	}

	public function getCooldown() : int{
		return max($this->cooldown - time(), 0);
	}

	public function setCooldown(int $cooldown) : void{
		$this->cooldown = $cooldown > 0 ? time() + $cooldown : null;
	}

	public function getExperience() : SkillExperienceInstance{
		return $this->experience;
	}
}