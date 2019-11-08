<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player;

use cosmicpe\mcmmo\skill\Skill;
use cosmicpe\mcmmo\skill\SkillInstance;
use pocketmine\utils\UUID;

final class Player{

	/** @var UUID */
	private $uuid;

	/** @var SkillInstance[] */
	private $skills;

	/**
	 * @param UUID $uuid
	 * @param SkillInstance[] $skills
	 */
	public function __construct(UUID $uuid, array $skills = []){
		$this->uuid = $uuid;
		$this->skills = $skills;
	}

	public function getUniqueId() : UUID{
		return $this->uuid;
	}

	/**
	 * @return SkillInstance[]
	 */
	public function getSkills() : array{
		return $this->skills;
	}

	public function getSkill(Skill $skill) : SkillInstance{
		return $this->skills[$identifier = $skill->getIdentifier()] ?? $this->skills[$identifier] = new SkillInstance($skill);
	}
}