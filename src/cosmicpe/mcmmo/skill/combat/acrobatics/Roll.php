<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\skill\subskill\ProbableSubSkill;

class Roll extends ProbableSubSkill{

	/** @var float */
	private $damage_reduction;

	public function __construct(int $max_level, float $max_chance, float $damage_reduction){
		parent::__construct($max_level, $max_chance);
		$this->damage_reduction = $damage_reduction;
	}

	public function getDamageReduction(bool $graceful) : float{
		return $this->damage_reduction * ($graceful ? 2 : 1);
	}
}