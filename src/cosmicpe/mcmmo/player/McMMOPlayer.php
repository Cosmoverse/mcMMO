<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player;

use cosmicpe\mcmmo\event\McMMOPlayerSkillExperienceChangeEvent;
use cosmicpe\mcmmo\event\McMMOPlayerSkillLevelChangeEvent;
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
		$experience = $this->getSkill($skill)->getExperience();
		$ev = McMMOPlayerSkillExperienceChangeEvent::createInstance($this, $skill, $experience->getValue() + $value);
		$ev->call();
		if(!$ev->isCancelled()){
			$experience->setValue($ev->getNewExperience());
			if($ev instanceof McMMOPlayerSkillLevelChangeEvent){
				$player = $this->getPlayer();
				if($player !== null){
					$new_level = $ev->getNewLevel();
					$player->sendMessage(TextFormat::YELLOW . $skill->getName() . " increased by " . ($new_level - $ev->getOldLevel()) . ". Total (" . $new_level . ")");
					$player->getWorld()->addSound($player->getEyePos(), new McMMOLevelUpSound(), [$player]);
				}
			}
			return true;
		}
		return false;
	}
}