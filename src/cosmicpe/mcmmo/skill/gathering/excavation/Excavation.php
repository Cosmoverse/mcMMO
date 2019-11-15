<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use cosmicpe\mcmmo\skill\gathering\GatheringSkill;
use cosmicpe\mcmmo\skill\Listenable;
use cosmicpe\mcmmo\skill\SkillInstance;
use Generator;
use pocketmine\block\Block;
use pocketmine\item\ItemFactory;

class Excavation implements GatheringSkill, Listenable{

	/** @var int[] */
	protected $block_xp = [];

	/** @var ExcavationLootTable */
	protected $treasures;

	public function __construct(array $blocks_xp_config, array $treasure_config){
		foreach($blocks_xp_config as $block_string => $xp){
			$this->block_xp[ItemFactory::fromString($block_string)->getBlock()->getId()] = $xp;
		}

		$this->treasures = new ExcavationLootTable();
		foreach($treasure_config as $item_string => [
			"amount" => $amount,
			"xp" => $xp,
			"drop" => $drop_config
		]){
			$this->treasures->add(new ExcavationLootTableEntry(
				ItemFactory::fromString($item_string)->setCount($amount),
				$xp,
				$drop_config["level"],
				$drop_config["from"],
			), $drop_config["chance"]);
		}
		$this->treasures->setup(true);
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

	/**
	 * @param Block $block
	 * @param SkillInstance $skill
	 * @return ExcavationLootTableEntry[]|Generator
	 */
	public function getTreasure(Block $block, SkillInstance $skill) : Generator{
		$level = $skill->getExperience()->getLevel();
		foreach($this->treasures->generate(3) as $entry){
			if($entry->isBlockApplicable($block) && $level >= $entry->getLevelRequirement()){
				yield $entry;
			}
		}
	}
}