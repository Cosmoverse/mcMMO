<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player;

use cosmicpe\mcmmo\event\player\skill\McMMOPlayerSkillExperienceChangeEvent;
use cosmicpe\mcmmo\skill\experience\SkillExperienceManager;
use cosmicpe\mcmmo\skill\Skill;
use cosmicpe\mcmmo\skill\SkillInstance;
use cosmicpe\mcmmo\skill\subskill\SubSkill;
use cosmicpe\mcmmo\skill\subskill\SubSkillInstance;
use cosmicpe\mcmmo\sound\McMMOLevelUpSound;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Ramsey\Uuid\UuidInterface;

final class McMMOPlayer{

	private PlayerAbilityHandler $ability_handler;

	/**
	 * @param UuidInterface $uuid
	 * @param array<string, SkillInstance> $skills
	 * @param array<string, SubSkillInstance> $sub_skills
	 */
	public function __construct(
		private UuidInterface $uuid,
		private array $skills = [],
		private array $sub_skills = []
	){
		$this->ability_handler = new PlayerAbilityHandler($this);
	}

	public function onDisconnect() : void{
		$this->ability_handler->destroy();
	}

	public function getUniqueId() : UuidInterface{
		return $this->uuid;
	}

	public function getPlayer() : ?Player{
		return Server::getInstance()->getPlayerByUUID($this->uuid);
	}

	/**
	 * @return array<string, SkillInstance>
	 */
	public function getSkills() : array{
		return $this->skills;
	}

	public function getSkill(Skill $skill) : SkillInstance{
		return $this->skills[$skill->getIdentifier()] ??= new SkillInstance($skill);
	}

	/**
	 * @return array<string, SubSkillInstance>
	 */
	public function getSubSkills() : array{
		return $this->sub_skills;
	}

	public function getSubSkill(SubSkill $skill) : SubSkillInstance{
		return $this->sub_skills[$skill->getIdentifier()] ??= new SubSkillInstance($skill);
	}

	public function getAbilityHandler() : PlayerAbilityHandler{
		return $this->ability_handler;
	}

	public function increaseSkillExperience(Skill $skill, int $value, ?int $cause = null) : bool{
		return $this->setSkillExperience($skill, $this->getSkill($skill)->getExperience()->getValue() + $value, $cause);
	}

	public function decreaseSkillExperience(Skill $skill, int $value, ?int $cause = null) : bool{
		return $this->setSkillExperience($skill, $this->getSkill($skill)->getExperience()->getValue() - $value, $cause);
	}

	public function setSkillExperience(Skill $skill, int $value, ?int $cause = null) : bool{
		$skill_instance = $this->getSkill($skill);
		$experience = $skill_instance->getExperience();
		$ev = new McMMOPlayerSkillExperienceChangeEvent($this, $skill, $skill_instance->getExperience()->getValue(), $value, $cause ?? McMMOPlayerSkillExperienceChangeEvent::CAUSE_CUSTOM);
		$ev->call();
		if(!$ev->isCancelled()){
			$experience->setValue($ev->getNewExperience());
			$old_level = $ev->getOldLevel();
			$new_level = $ev->getNewLevel();
			if($new_level > $old_level){
				$player = $this->getPlayer();
				if($player !== null){
					$increase = $new_level - $old_level;
					$player->sendMessage(TextFormat::YELLOW . "{$skill->getName()} increased by {$increase}. Total ({$new_level})");
					$player->getWorld()->addSound($player->getEyePos(), new McMMOLevelUpSound(), [$player]);
				}
			}
			return true;
		}
		return false;
	}

	public function increaseSkillLevel(Skill $skill, int $value, ?int $cause = null) : bool{
		return $this->setSkillLevel($skill, $this->getSkill($skill)->getExperience()->getLevel() + $value, $cause);
	}

	public function decreaseSkillLevel(Skill $skill, int $value, ?int $cause = null) : bool{
		return $this->setSkillLevel($skill, $this->getSkill($skill)->getExperience()->getLevel() - $value, $cause);
	}

	public function setSkillLevel(Skill $skill, int $value, ?int $cause = null) : bool{
		return $this->setSkillExperience($skill, SkillExperienceManager::get()->getExperienceFromLevel($value), $cause);
	}
}