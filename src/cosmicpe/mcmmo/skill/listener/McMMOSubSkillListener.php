<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use cosmicpe\mcmmo\event\player\McMMOPlayerAbilityActivateEvent;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\ability\Ability;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class McMMOSubSkillListener{

	/** @var Ability[][] */
	private static $abilities = [];

	public static function init() : void{
		McMMOSkillListener::registerEvent(EventPriority::NORMAL, static function(PlayerItemUseEvent $event, Player $player, McMMOPlayer $mcmmo_player) : void{
			$handler = $mcmmo_player->getAbilityHandler();
			if($handler->getCurrent() === null){
				$item = $event->getItem();
				$type = $item->getBlockToolType();
				if(isset(self::$abilities[$type])){
					foreach(self::$abilities[$type] as $ability){
						$sub_skill = SubSkillManager::get($ability->getSubSkillIdentifier());
						$sub_skill_instance = $mcmmo_player->getSubSkill($sub_skill);
						$cooldown = $sub_skill_instance->getCooldown();
						if($cooldown === 0){
							$duration = $ability->getDuration($player, $mcmmo_player, $item);
							if($duration > 0){
								$ev = new McMMOPlayerAbilityActivateEvent($mcmmo_player, $ability, $duration, $sub_skill->getCooldown());
								$ev->call();
								if(!$ev->isCancelled()){
									$ability->onAdd($player, $mcmmo_player, $item);
									$sub_skill_instance->setCooldown($ev->getCooldown());
									$player->sendMessage(TextFormat::GREEN . "* {$sub_skill->getName()} ACTIVATED *");
									$handler->setCurrent($ability, $ev->getDuration(), static function() use ($player, $sub_skill) : void{
										if($player->isOnline()){
											$player->sendMessage(TextFormat::RED . "* {$sub_skill->getName()} DEACTIVATED *");
										}
									});
									break;
								}
							}
						}else{
							$player->sendMessage(TextFormat::RED . "You cannot use {$sub_skill->getName()} for another {$cooldown}s!");
						}
					}
				}
			}
		});
	}

	public static function registerAbility(Ability $ability) : void{
		self::$abilities[$ability->getToolType()][get_class($ability)] = $ability;
	}
}