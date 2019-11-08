<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use cosmicpe\mcmmo\player\Player as McMMOPlayer;
use cosmicpe\mcmmo\player\PlayerManager;
use pocketmine\event\Listener;
use pocketmine\player\Player;

abstract class SkillListener implements Listener{

	/** @var PlayerManager */
	protected $manager;

	public function __construct(PlayerManager $manager){
		$this->manager = $manager;
	}

	public function getMcMMOPlayer(Player $player) : ?McMMOPlayer{
		return $this->manager->get($player);
	}
}