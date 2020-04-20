<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering;

use cosmicpe\mcmmo\integration\FireworkWrapper;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\McMMOPlayer;
use pocketmine\item\Item;
use pocketmine\player\Player;

trait GatheringAbilityTrait{

	public function onAdd(Player $player, McMMOPlayer $mcmmo_player, Item $item) : void{
		McMMO::getInstance()->getIntegrationManager()->getFireworkWrapper()->spawn($player->getPosition(), FireworkWrapper::TYPE_HUGE_SPHERE, FireworkWrapper::COLOR_GREEN);
	}

	public function onRemove(McMMOPlayer $mcmmo_player) : void{
		$player = $mcmmo_player->getPlayer();
		if($player !== null){
			McMMO::getInstance()->getIntegrationManager()->getFireworkWrapper()->spawn($player->getPosition(), FireworkWrapper::TYPE_HUGE_SPHERE, FireworkWrapper::COLOR_RED);
		}
	}
}