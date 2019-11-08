<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\player\PlayerManager;
use cosmicpe\mcmmo\skill\SkillIds;
use cosmicpe\mcmmo\skill\SkillListener;
use cosmicpe\mcmmo\skill\SkillManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class AcrobaticsListener extends SkillListener{

	/** @var Acrobatics */
	private $acrobatics;

	public function __construct(PlayerManager $manager){
		parent::__construct($manager);
		$this->acrobatics = SkillManager::get(SkillIds::ACROBATICS);
	}

	/**
	 * @param EntityDamageEvent $event
	 * @priority MONITOR
	 */
	public function onEntityDamage(EntityDamageEvent $event) : void{
		if($event->getCause() === EntityDamageEvent::CAUSE_FALL){
			$player = $event->getEntity();
			if($player instanceof Player){
				$mcmmo_player = $this->getMcMMOPlayer($player);
				if($mcmmo_player !== null){
					$mcmmo_player->getSkill($this->acrobatics)->addExperience($this->acrobatics->getFallXp($player, $event->getFinalDamage()));
				}
				var_dump($mcmmo_player);
			}
		}
	}
}