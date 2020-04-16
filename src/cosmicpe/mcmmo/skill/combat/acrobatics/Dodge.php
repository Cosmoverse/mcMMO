<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\skill\combat\CombatSubSkillIds;
use cosmicpe\mcmmo\utils\NumberUtils;
use Ds\Set;
use InvalidArgumentException;
use pocketmine\event\entity\EntityDamageEvent;
use ReflectionClass;

class Dodge extends AcrobaticsSubSkill{

	/** @var int */
	protected $max_level;

	/** @var float */
	protected $max_chance;

	/** @var int */
	protected $min_level;

	/** @var float */
	private $damage_amplifier;

	/** @var Set<int> */
	private $disallowed_causes;

	public function __construct(int $min_level, int $max_level, float $max_chance, float $damage_amplifier, array $disallowed_causes){
		$this->max_level = $max_level;
		$this->max_chance = $max_chance;
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

	public function getIdentifier() : string{
		return CombatSubSkillIds::DODGE;
	}

	public function getName() : string{
		return "Dodge";
	}

	public function getDamageAmplifier() : float{
		return $this->damage_amplifier;
	}

	public function isCauseAllowed(int $cause) : bool{
		return !$this->disallowed_causes->contains($cause);
	}

	public function getChance(int $level, float $amplifier = 1.0) : float{
		return $level >= $this->min_level ? $amplifier * $this->max_chance * (min($level, $this->max_level) / $this->max_level) : 0.0;
	}

	public function process(int $level, float $amplifier = 1.0) : bool{
		return NumberUtils::getRandomBool($this->getChance($level, $amplifier));
	}
}