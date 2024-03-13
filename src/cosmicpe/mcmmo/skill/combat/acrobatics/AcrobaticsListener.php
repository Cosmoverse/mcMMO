<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\event\player\skill\combat\McMMOPlayerDodgeEvent;
use cosmicpe\mcmmo\event\player\skill\combat\McMMOPlayerRollEvent;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\listener\McMMOExperienceToller;
use cosmicpe\mcmmo\skill\listener\McMMOSkillListener;
use cosmicpe\mcmmo\skill\SkillIds;
use cosmicpe\mcmmo\skill\SkillManager;
use cosmicpe\mcmmo\skill\subskill\SubSkillIds;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AcrobaticsListener implements Listener{

	public function __construct(){
		/** @var Acrobatics $acrobatics */
		$acrobatics = SkillManager::get(SkillIds::ACROBATICS);

		/** @var Dodge $dodge */
		$dodge = SubSkillManager::get(SubSkillIds::DODGE);

		McMMOSkillListener::registerEvent(EventPriority::NORMAL, static function(EntityDamageEvent $event, Player $player, McMMOPlayer $mcmmo_player, McMMOExperienceToller $toller) use($acrobatics, $dodge) : void{
			if($dodge->isCauseAllowed($event->getCause())){
				$damage = $event->getFinalDamage();
				if($damage < $player->getHealth() && $dodge->process($mcmmo_player->getSkill($acrobatics)->getExperience()->getLevel())){
					($ev = new McMMOPlayerDodgeEvent($mcmmo_player, $acrobatics, $event))->call();
					if(!$ev->isCancelled()){
						$event->setBaseDamage($event->getBaseDamage() * 0.5);
						$toller->add($acrobatics, (int) floor(120 * $damage), static function() use($player) : void{
							$player->sendMessage(TextFormat::GREEN . "**Dodged**");
						});
					}
				}
			}
		});

		/** @var Roll $roll */
		$roll = SubSkillManager::get(SubSkillIds::ROLL);

		McMMOSkillListener::registerEvent(EventPriority::HIGH, static function(EntityDamageEvent $event, Player $player, McMMOPlayer $mcmmo_player, McMMOExperienceToller $toller) use($acrobatics, $roll) : void{
			if($event->getCause() === EntityDamageEvent::CAUSE_FALL){
				$skill = $mcmmo_player->getSkill($acrobatics);
				$damage = $event->getFinalDamage();
				$graceful = $player->isSneaking();
				if($roll_processed = $roll->process($skill->getExperience()->getLevel(), $graceful ? Roll::GRACEFUL_ROLL_AMPLIFIER : Roll::DEFAULT_ROLL_AMPLIFIER)){
					($ev = new McMMOPlayerRollEvent($mcmmo_player, $acrobatics, $event))->call();
					if(!$ev->isCancelled()){
						$event->setBaseDamage(max(0, $event->getBaseDamage() - $roll->getDamageReduction($graceful)));
						$cb = static function() use ($player, $graceful) : void{
							$player->sendMessage($graceful ? TextFormat::GREEN . "**Graceful Landing**" : TextFormat::ITALIC . "**Rolled**");
						};
					}else{
						$cb = null;
					}
				}else{
					$cb = null;
				}
				$toller->add($acrobatics, $acrobatics->getFallXp($player, $damage, $roll_processed), $cb);
			}
		});
	}
}