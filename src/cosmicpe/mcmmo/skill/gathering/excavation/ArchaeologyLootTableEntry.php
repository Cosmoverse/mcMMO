<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;

final class ArchaeologyLootTableEntry{

	private Item $item;
	private int $experience;
	private int $level_requirement;

	/** @var int[] */
	private array $applicable_blocks = [];

	/**
	 * @param Item $item
	 * @param int $experience
	 * @param int $level_requirement
	 * @param string[] $applicable_blocks
	 */
	public function __construct(Item $item, int $experience, int $level_requirement, array $applicable_blocks){
		$this->item = $item;
		$this->experience = $experience;
		$this->level_requirement = $level_requirement;

		foreach($applicable_blocks as $block_id){
			$block_id = LegacyStringToItemParser::getInstance()->parse($block_id)->getBlock()->getId();
			$this->applicable_blocks[$block_id] = $block_id;
		}
	}

	public function getExperience() : int{
		return $this->experience;
	}

	public function getLevelRequirement() : int{
		return $this->level_requirement;
	}

	public function getItem() : Item{
		return clone $this->item;
	}

	public function isBlockApplicable(Block $block) : bool{
		return isset($this->applicable_blocks[$block->getId()]);
	}
}