<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\ability;

use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\SkillManager;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class BuffableAbility implements Ability{

	final public function getDuration(Player $player, McMMOPlayer $mcmmo_player, Item $item) : int{
		/** @var BuffableAbilitySubSkill $sub_skill */
		$sub_skill = SubSkillManager::get($this->getSubSkillIdentifier());
		return $sub_skill->getLevelIncrease($mcmmo_player->getSkill(SkillManager::get($sub_skill->getParentIdentifier()))->getExperience()->getLevel());
	}
}