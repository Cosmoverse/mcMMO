<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event\player;

use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\ability\Ability;
use cosmicpe\mcmmo\skill\subskill\SubSkill;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use InvalidArgumentException;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class McMMOPlayerAbilityActivateEvent extends McMMOPlayerEvent implements Cancellable{
	use CancellableTrait;

	/** @var Ability */
	protected $ability;

	/** @var int */
	protected $duration;

	/** @var int|null */
	protected $cooldown;

	public function __construct(McMMOPlayer $player, Ability $ability, int $duration, int $cooldown){
		parent::__construct($player);
		$this->ability = $ability;
		$this->duration = $duration;
		$this->cooldown = $cooldown;
	}

	public function getAbility() : Ability{
		return $this->ability;
	}

	public function getSubSkill() : SubSkill{
		return SubSkillManager::get($this->ability->getSubSkillIdentifier());
	}

	public function getDuration() : int{
		return $this->duration;
	}

	public function setDuration(int $duration) : void{
		if($duration <= 0){
			throw new InvalidArgumentException("Duration cannot be <= 0, got {$duration}");
		}
		$this->duration = $duration;
	}

	public function getCooldown() : ?int{
		return $this->cooldown;
	}

	public function setCooldown(?int $cooldown) : void{
		$this->cooldown = $cooldown;
	}
}