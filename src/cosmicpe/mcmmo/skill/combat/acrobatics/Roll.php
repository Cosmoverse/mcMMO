<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\skill\combat\CombatSubSkillIds;
use cosmicpe\mcmmo\utils\NumberUtils;

class Roll extends AcrobaticsSubSkill{

	/** @var int */
	protected $max_level;

	/** @var float */
	protected $max_chance;

	/** @var float */
	private $damage_reduction;

	public function __construct(int $max_level, float $max_chance, float $damage_reduction){
		$this->max_level = $max_level;
		$this->max_chance = $max_chance;
		$this->damage_reduction = $damage_reduction;
	}

	public function getIdentifier() : string{
		return CombatSubSkillIds::ROLL;
	}

	public function getName() : string{
		return "Roll";
	}

	public function getDamageReduction(bool $graceful) : float{
		return $this->damage_reduction * ($graceful ? 2 : 1);
	}

	public function process(int $level, float $amplifier = 1.0) : bool{
		return NumberUtils::getRandomBool($amplifier * $this->max_chance * (min($level, $this->max_level) / $this->max_level));
	}
}