<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use cosmicpe\mcmmo\skill\ability\BuffableAbilitySubSkill;
use cosmicpe\mcmmo\skill\ability\utils\AbilityDuration;
use cosmicpe\mcmmo\skill\gathering\GatheringSubSkillIds;
use cosmicpe\mcmmo\skill\listener\McMMOSubSkillListener;

class GigaDrillBreaker extends ArchaeologySubSkill implements BuffableAbilitySubSkill{

	/** @var AbilityDuration */
	private $duration;

	/** @var int */
	private $enchantment_buff;

	/** @var int */
	private $cooldown;

	/**
	 * @param array<string, mixed> $config
	 */
	public function __construct(array $config){
		["duration" => $duration_config, "enchant-buff" => $enchantment_buff, "cooldown" => $cooldown] = $config;
		$this->duration = AbilityDuration::parse($duration_config);
		$this->enchantment_buff = (int) $enchantment_buff;
		$this->cooldown = (int) $cooldown;
		McMMOSubSkillListener::registerAbility(new GigaDrillBreakerAbility());
	}

	public function getIdentifier() : string{
		return GatheringSubSkillIds::GIGA_DRILL_BREAKER;
	}

	public function getName() : string{
		return "Giga Drill Breaker";
	}

	public function getLevelIncrease(int $level) : int{
		return $this->duration->get($level);
	}

	public function getEnchantmentBuff() : int{
		return $this->enchantment_buff;
	}

	public function getCooldown() : int{
		return $this->cooldown;
	}
}