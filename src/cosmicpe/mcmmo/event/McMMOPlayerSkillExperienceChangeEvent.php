<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\event;

use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\experience\SkillExperienceManager;
use cosmicpe\mcmmo\skill\Skill;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class McMMOPlayerSkillExperienceChangeEvent extends McMMOPlayerSkillEvent implements Cancellable{
	use CancellableTrait;

	public static function createInstance(McMMOPlayer $player, Skill $skill, int $new_value) : McMMOPlayerSkillExperienceChangeEvent{
		$experience = $player->getSkill($skill)->getExperience();
		$old_level = $experience->getLevel();
		return SkillExperienceManager::get()->getLevelFromExperience($new_value) !== $old_level ?
			new McMMOPlayerSkillLevelChangeEvent($player, $skill, $experience->getValue(), $new_value, $old_level) :
		new McMMOPlayerSkillExperienceChangeEvent($player, $skill, $experience->getValue(), $new_value);
	}

	/** @var int */
	protected $old_experience;

	/** @var int */
	protected $new_experience;

	public function __construct(McMMOPlayer $player, Skill $skill, int $old_experience, int $new_experience){
		parent::__construct($player, $skill);
		$this->old_experience = $old_experience;
		$this->new_experience = $new_experience;
	}

	public function getOldExperience() : int{
		return $this->old_experience;
	}

	public function getNewExperience() : int{
		return $this->new_experience;
	}

	public function setNewExperience(int $value) : void{
		$this->new_experience = $value;
	}
}