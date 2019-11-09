<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\skill\SkillIds;
use cosmicpe\mcmmo\skill\SkillListener;
use cosmicpe\mcmmo\skill\SkillManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AcrobaticsListener extends SkillListener{

	/** @var Acrobatics */
	private $acrobatics;

	public function __construct(){
		$this->acrobatics = SkillManager::get(SkillIds::ACROBATICS);
	}

	/**
	 * @param EntityDamageEvent $event
	 * @priority NORMAL
	 */
	public function onEntityDamageDodge(EntityDamageEvent $event) : void{
		$dodge = $this->acrobatics->getDodge();
		if($dodge->isCauseAllowed($event->getCause())){
			$player = $event->getEntity();
			if($player instanceof Player && $event->getFinalDamage() < $player->getHealth()){
				$mcmmo_player = $this->getMcMMOPlayer($player);
				if($mcmmo_player !== null && $dodge->process($mcmmo_player->getSkill($this->acrobatics)->getExperience()->getLevel())){
					$event->setBaseDamage($event->getBaseDamage() * 0.5);
					$player->sendMessage(TextFormat::GREEN . "**Dodged**");
				}
			}
		}
	}

	/**
	 * @param EntityDamageEvent $event
	 * @priority HIGH
	 */
	public function onEntityDamageRollAndSkill(EntityDamageEvent $event) : void{
		if($event->getCause() === EntityDamageEvent::CAUSE_FALL){
			$player = $event->getEntity();
			if($player instanceof Player){
				$mcmmo_player = $this->getMcMMOPlayer($player);
				if($mcmmo_player !== null){
					$skill = $mcmmo_player->getSkill($this->acrobatics);
					$roll = $this->acrobatics->getRoll();
					$damage = $event->getFinalDamage();
					$graceful = $player->isSneaking();
					if($roll_processed = $roll->process($skill->getExperience()->getLevel(), $graceful ? 2.0 : 1.0)){
						$new_damage = $event->getBaseDamage() - $roll->getDamageReduction($graceful);
						if($new_damage > 0){
							$event->setBaseDamage($new_damage);
						}else{
							$event->setCancelled();
						}
						$player->sendMessage($graceful ? TextFormat::GREEN . "**Graceful Landing**" : TextFormat::ITALIC . "**Rolled**");
					}
					$mcmmo_player->increaseSkillExperience($this->acrobatics, $this->acrobatics->getFallXp($player, $damage, $roll_processed));
				}
			}
		}
	}
}