<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\subskill;

use cosmicpe\mcmmo\utils\NumberUtils;

abstract class ProbableSubSkill extends SubSkill{

	/** @var float */
	protected $max_chance;

	public function __construct(int $max_level, float $max_chance){
		parent::__construct($max_level);
		$this->max_chance = $max_chance;
	}

	public function process(int $level, float $amplifier = 1.0) : bool{
		return NumberUtils::getRandomBool($amplifier * $this->max_chance * (min($level, $this->max_level) / $this->max_level));
	}
}