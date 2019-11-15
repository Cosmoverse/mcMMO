<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\ability;

use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\SkillManager;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

abstract class BuffableAbility implements Ability{

	final public function handle(Player $player, McMMOPlayer $mcmmo_player, Item $item) : ?int{
		/** @var BuffableAbilitySubSkill $sub_skill */
		$sub_skill = SubSkillManager::get($this->getSubSkillIdentifier());
		$sub_skill_instance = $mcmmo_player->getSubSkill($sub_skill);
		$cooldown = $sub_skill_instance->getCooldown();
		if($cooldown === 0){
			if($this->handleBuffed($player, $mcmmo_player, $item)){
				return $sub_skill->getLevelIncrease($mcmmo_player->getSkill(SkillManager::get($sub_skill->getParentIdentifier()))->getExperience()->getLevel());
			}
		}else{
			$player->sendMessage(TextFormat::RED . "You cannot use this ability for another " . $cooldown . "s!");
		}

		return null;
	}

	abstract protected function handleBuffed(Player $player, McMMOPlayer $mcmmo_player, Item $item) : bool;
}