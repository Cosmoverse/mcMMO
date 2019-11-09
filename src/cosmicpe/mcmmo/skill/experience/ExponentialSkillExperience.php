<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\experience;

class ExponentialSkillExperience implements SkillExperience{

	/** @var float */
	private $constant;

	/** @var float */
	private $multiplier;

	/** @var float */
	private $exponent;

	public function __construct(float $constant, float $multiplier, float $exponent){
		$this->constant = $constant;
		$this->multiplier = $multiplier;
		$this->exponent = $exponent;
	}

	public function getExperienceFromLevel(int $level) : int{
		return (int) floor($this->constant * (($level * $this->multiplier) ** $this->exponent));
	}

	public function getLevelFromExperience(int $experience) : int{
		return (int) ceil((($experience / $this->constant) ** (1 / $this->exponent)) / $this->multiplier);
	}
}
