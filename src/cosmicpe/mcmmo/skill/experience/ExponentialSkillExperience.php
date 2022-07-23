<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\experience;

class ExponentialSkillExperience implements SkillExperience{

	public function __construct(
		private float $constant,
		private float $multiplier,
		private float $exponent
	){}

	public function getExperienceFromLevel(int $level) : int{
		return (int) floor($this->constant * (($level * $this->multiplier) ** $this->exponent));
	}

	public function getLevelFromExperience(int $experience) : int{
		return (int) ceil((($experience / $this->constant) ** (1 / $this->exponent)) / $this->multiplier);
	}
}
