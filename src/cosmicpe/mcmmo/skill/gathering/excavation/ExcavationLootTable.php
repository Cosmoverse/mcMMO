<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use cosmicpe\mcmmo\utils\WeightedRandom;
use Generator;

final class ExcavationLootTable extends WeightedRandom{

	/**
	 * @param int $count
	 * @return ExcavationLootTableEntry[]|Generator
	 */
	public function generate(int $count) : Generator{
		foreach($this->generateIndexes($count) as $index){
			$result = $this->indexes[$index] ?? null;
			if($result !== null){
				yield $result;
			}
		}
	}
}