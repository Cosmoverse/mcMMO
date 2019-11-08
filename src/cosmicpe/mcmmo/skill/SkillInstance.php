<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use InvalidArgumentException;

final class SkillInstance{

	/** @var Skill */
	private $skill;

	/** @var int|null */
	private $cooldown;

	/** @var int */
	private $experience;

	public function __construct(Skill $skill, ?int $cooldown = null, int $experience = 0){
		$this->skill = $skill;
		$this->cooldown = $cooldown;
		$this->experience = $experience;
	}

	public function getSkill() : Skill{
		return $this->skill;
	}

	public function getCooldown() : int{
		return max($this->cooldown - time(), 0);
	}

	public function getExperience() : int{
		return $this->experience;
	}

	public function setCooldown(int $cooldown) : void{
		$this->cooldown = $cooldown > 0 ? time() + $cooldown : null;
	}

	public function setExperience(int $experience) : void{
		if($experience < 0){
			throw new InvalidArgumentException("Experience must be > 0, got " . $experience);
		}
		$this->experience = $experience;
	}

	public function addExperience(int $experience) : void{
		if($experience < 0){
			throw new InvalidArgumentException("Experience must be > 0, got " . $experience);
		}
		$this->setExperience($this->experience + $experience);
	}
}