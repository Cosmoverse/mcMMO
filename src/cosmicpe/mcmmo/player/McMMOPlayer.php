<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player;

use cosmicpe\mcmmo\event\McMMOPlayerSkillExperienceChangeEvent;
use cosmicpe\mcmmo\skill\Skill;
use cosmicpe\mcmmo\skill\SkillInstance;
use cosmicpe\mcmmo\sound\McMMOLevelUpSound;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;

final class McMMOPlayer{

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

	public function getPlayer() : ?Player{
		return Server::getInstance()->getPlayerByUUID($this->uuid);
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

	public function increaseSkillExperience(Skill $skill, int $value) : bool{
		return $this->setSkillExperience($skill, $this->getSkill($skill)->getExperience()->getValue() + $value);
	}

	public function decreaseSkillExperience(Skill $skill, int $value) : bool{
		return $this->setSkillExperience($skill, $this->getSkill($skill)->getExperience()->getValue() - $value);
	}

	public function setSkillExperience(Skill $skill, int $value) : bool{
		$skill_instance = $this->getSkill($skill);
		$experience = $skill_instance->getExperience();
		$ev = new McMMOPlayerSkillExperienceChangeEvent($this, $skill, $skill_instance->getExperience()->getValue(), $value);
		$ev->call();
		if(!$ev->isCancelled()){
			$experience->setValue($ev->getNewExperience());
			$old_level = $ev->getOldLevel();
			$new_level = $ev->getNewLevel();
			if($new_level > $old_level){
				$player = $this->getPlayer();
				if($player !== null){
					$player->sendMessage(TextFormat::YELLOW . $skill->getName() . " increased by " . ($new_level - $old_level) . ". Total (" . $new_level . ")");
					$player->getWorld()->addSound($player->getEyePos(), new McMMOLevelUpSound(), [$player]);
				}
			}
			return true;
		}
		return false;
	}
}