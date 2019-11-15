<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\listener\McMMOExperienceToller;
use cosmicpe\mcmmo\skill\listener\McMMOSkillListener;
use cosmicpe\mcmmo\skill\SkillIds;
use cosmicpe\mcmmo\skill\SkillManager;
use cosmicpe\mcmmo\skill\subskill\SubSkillIds;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class ExcavationListener implements Listener{

	public function __construct(){
		/** @var Excavation $excavation */
		$excavation = SkillManager::get(SkillIds::EXCAVATION);
		$archaeology = SubSkillManager::get(SubSkillIds::ARCHAEOLOGY);
		McMMOSkillListener::registerEvent(EventPriority::NORMAL, static function(BlockBreakEvent $event, Player $player, McMMOPlayer $mcmmo_player, McMMOExperienceToller $toller) use($excavation, $archaeology) : void{
			$block = $event->getBlock();
			$drops = $event->getDrops();
			$xp = $excavation->getBlockExperience($block);
			/** @var ArchaeologyLootTableEntry $treasure */
			foreach($archaeology->getTreasures($block, $mcmmo_player->getSkill($excavation)) as $treasure){
				$drops[] = $treasure->getItem();
				$xp += $treasure->getExperience();
			}
			$event->setDrops($drops);
			$toller->add($excavation, $xp);
		});
	}
}