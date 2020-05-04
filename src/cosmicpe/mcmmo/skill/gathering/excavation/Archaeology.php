<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use cosmicpe\mcmmo\skill\gathering\GatheringSubSkillIds;
use cosmicpe\mcmmo\skill\SkillInstance;
use Generator;
use pocketmine\block\Block;
use pocketmine\item\ItemFactory;

class Archaeology extends ArchaeologySubSkill{

	/** @var int */
	private $max_loot_drops;

	/** @var ArchaeologyLootTable */
	protected $loot_table;

	/**
	 * @param array<string, mixed> $archaeology_config
	 */
	public function __construct(array $archaeology_config){
		$this->max_loot_drops = $archaeology_config["max-drops"];
		$this->loot_table = new ArchaeologyLootTable();
		foreach($archaeology_config["drops"] as $item_string => [
			"amount" => $amount,
			"xp" => $xp,
			"drop" => $drop_config
		]){
			$this->loot_table->add(new ArchaeologyLootTableEntry(
				ItemFactory::getInstance()->fromString($item_string)->setCount($amount),
				$xp,
				$drop_config["level"],
				$drop_config["from"],
			), $drop_config["chance"]);
		}
		$this->loot_table->setup();
	}

	public function getIdentifier() : string{
		return GatheringSubSkillIds::ARCHAEOLOGY;
	}

	public function getName() : string{
		return "Archaeology";
	}

	/**
	 * @param Block $block
	 * @param SkillInstance $skill
	 * @return ArchaeologyLootTableEntry[]|Generator
	 */
	public function getTreasures(Block $block, SkillInstance $skill) : Generator{
		$level = $skill->getExperience()->getLevel();
		foreach($this->loot_table->generate($this->max_loot_drops) as $entry){
			if($entry->isBlockApplicable($block) && $level >= $entry->getLevelRequirement()){
				yield $entry;
			}
		}
	}

	public function getCooldown() : int{
		return 0;
	}
}