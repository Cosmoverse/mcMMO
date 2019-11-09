<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\skill\subskill\ProbableSubSkill;
use Ds\Set;
use InvalidArgumentException;
use pocketmine\event\entity\EntityDamageEvent;
use ReflectionClass;

class Dodge extends ProbableSubSkill{

	/** @var int */
	protected $min_level;

	/** @var float */
	private $damage_amplifier;

	/** @var Set<int> */
	private $disallowed_causes;

	public function __construct(int $min_level, int $max_level, float $max_chance, float $damage_amplifier, array $disallowed_causes){
		parent::__construct($max_level, $max_chance);
		$this->min_level = $min_level;
		$this->damage_amplifier = $damage_amplifier;

		$constants = (new ReflectionClass(EntityDamageEvent::class))->getConstants();
		foreach($disallowed_causes as $k => $cause){
			if(isset($constants[$cause_uppercase = strtoupper($cause)])){
				$disallowed_causes[$k] = $constants[$cause_uppercase];
			}elseif(!is_int($cause)){
				throw new InvalidArgumentException("Invalid damage cause " . $cause . " for Dodge subskill.");
			}
		}

		$this->disallowed_causes = new Set($disallowed_causes);
	}

	public function getDamageAmplifier() : float{
		return $this->damage_amplifier;
	}

	public function isCauseAllowed(int $cause) : bool{
		return !$this->disallowed_causes->contains($cause);
	}

	public function process(int $level, float $amplifier = 1.0) : bool{
		return $level >= $this->min_level && parent::process($level, $amplifier);
	}
}