<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\skill\combat\CombatSkill;
use cosmicpe\mcmmo\skill\combat\CombatSkillIds;
use cosmicpe\mcmmo\skill\Listenable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\player\Player;

class Acrobatics implements CombatSkill, Listenable{

	public function getFallXp(Player $player, float $damage, bool $roll_processed) : int{
		$feather_falling_amp = 1;
		$feather_falling = Enchantment::FEATHER_FALLING();
		foreach($player->getArmorInventory()->getContents() as $item){
			if($item->hasEnchantment($feather_falling)){
				$feather_falling_amp = 2;
				break;
			}
		}
		return (int) floor(($roll_processed ? 80 : 120) * $damage * $feather_falling_amp);
	}

	public function getIdentifier() : string{
		return CombatSkillIds::ACROBATICS;
	}

	public function getName() : string{
		return "Acrobatics";
	}

	public function getListeners() : array{
		return [new AcrobaticsListener()];
	}
}