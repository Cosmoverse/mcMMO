<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use cosmicpe\mcmmo\command\McMMOSkillCommand;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\gathering\GatheringSkill;
use cosmicpe\mcmmo\skill\Listenable;
use cosmicpe\mcmmo\skill\subskill\SubSkillIds;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

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

	public function onSkillCommandRegister(McMMOSkillCommand $command) : void{
		$command->registerCommandWildcard("{GIGA_DRILL_BREAKER_LENGTH}", function(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string{
			/** @var GigaDrillBreaker $giga_drill_breaker */
			$giga_drill_breaker = SubSkillManager::get(SubSkillIds::GIGA_DRILL_BREAKER);
			return number_format($giga_drill_breaker->getLevelIncrease($mcmmo_player->getSkill($this)->getExperience()->getLevel()));
		});

		$compatible_materials = [];
		foreach(array_keys($this->block_xp) as $identifier){
			$compatible_materials[] = BlockFactory::get($identifier)->getName();
		}
		$compatible_materials = array_unique($compatible_materials);
		sort($compatible_materials);
		$compatible_materials = implode(", ", $compatible_materials);
		$command->registerGuideWildcard("{COMPATIBLE_MATERIALS}", static function(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) use($compatible_materials) : string{
			return $compatible_materials;
		});
	}
}