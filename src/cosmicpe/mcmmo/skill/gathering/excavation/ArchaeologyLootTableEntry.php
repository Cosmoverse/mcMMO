<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use Ds\Set;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

final class ArchaeologyLootTableEntry{

	/** @var Item */
	private $item;

	/** @var int */
	private $experience;

	/** @var int */
	private $level_requirement;

	/** @var Set<int> */
	private $applicable_blocks;

	public function __construct(Item $item, int $experience, int $level_requirement, array $applicable_blocks){
		$this->item = $item;
		$this->experience = $experience;
		$this->level_requirement = $level_requirement;

		$this->applicable_blocks = new Set();
		foreach($applicable_blocks as $block_id){
			$this->applicable_blocks->add(ItemFactory::fromString($block_id)->getBlock()->getId());
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
		return $this->applicable_blocks->contains($block->getId());
	}
}