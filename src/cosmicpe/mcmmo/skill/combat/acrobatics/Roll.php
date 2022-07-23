<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\skill\combat\CombatSubSkillIds;
use cosmicpe\mcmmo\utils\NumberUtils;

class Roll extends AcrobaticsSubSkill{

	public const GRACEFUL_ROLL_AMPLIFIER = 2.0;
	public const DEFAULT_ROLL_AMPLIFIER = 1.0;

	public function __construct(
		protected int $max_level,
		protected float $max_chance,
		private float $damage_reduction
	){}

	public function getIdentifier() : string{
		return CombatSubSkillIds::ROLL;
	}

	public function getName() : string{
		return "Roll";
	}

	public function getDamageReduction(bool $graceful) : float{
		return $this->damage_reduction * ($graceful ? 2 : 1);
	}

	public function getChance(int $level, float $amplifier = self::DEFAULT_ROLL_AMPLIFIER) : float{
		return $amplifier * $this->max_chance * (min($level, $this->max_level) / $this->max_level);
	}

	public function process(int $level, float $amplifier = self::DEFAULT_ROLL_AMPLIFIER) : bool{
		return NumberUtils::getRandomBool($this->getChance($level, $amplifier));
	}

	public function getCooldown() : int{
		return 0;
	}
}