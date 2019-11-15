<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

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
							$duration = $ability->handle($player, $mcmmo_player, $item);
							if($duration !== null){
								$player->sendMessage(TextFormat::GREEN . "* " . $sub_skill->getName() . " ACTIVATED *");
								$handler->setCurrent($ability, $duration, static function() use ($player, $sub_skill) : void{
									if($player->isOnline()){
										$player->sendMessage(TextFormat::RED . "* " . $sub_skill->getName() . " DEACTIVATED *");
									}
								});
								break;
							}
						}else{
							$player->sendMessage(TextFormat::RED . "You cannot use " . $sub_skill->getName() . " for another " . $cooldown . "s!");
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