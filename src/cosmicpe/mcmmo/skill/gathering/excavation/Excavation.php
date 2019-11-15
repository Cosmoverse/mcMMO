<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use cosmicpe\mcmmo\skill\gathering\GatheringSkill;
use cosmicpe\mcmmo\skill\Listenable;
use pocketmine\block\Block;
use pocketmine\item\ItemFactory;

class Excavation implements GatheringSkill, Listenable{

	/** @var int[] */
	protected $block_xp = [];

	public function __construct(array $blocks_xp_config){
		foreach($blocks_xp_config as $block_string => $xp){
			$this->block_xp[ItemFactory::fromString($block_string)->getBlock()->getId()] = $xp;
		}
	}

	public function getListeners() : array{
		return [new ExcavationListener()];
	}

	public function getIdentifier() : string{
		return "mcmmo:excavation";
	}

	public function getName() : string{
		return "Excavation";
	}

	public function getBlockExperience(Block $block) : int{
		return $this->block_xp[$block->getId()] ?? 0;
	}
}