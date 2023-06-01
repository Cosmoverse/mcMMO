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
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;

class Excavation implements GatheringSkill, Listenable{

	/** @var array<int, int> */
	protected array $block_xp = [];

	/** @var array<int, Block> */
	protected array $block_mapping = [];

	/**
	 * @param array<string, int> $blocks_xp_config
	 */
	public function __construct(array $blocks_xp_config){
		foreach($blocks_xp_config as $block_string => $xp) {
			$item = StringToItemParser::getInstance()->parse($block_string) ?? LegacyStringToItemParser::getInstance()->parse($block_string);
			$block = $item->getBlock();
			$this->block_xp[$block->getTypeId()] = $xp;
			$this->block_mapping[$block->getTypeId()] = $block;
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
		return $this->block_xp[$block->getTypeId()] ?? 0;
	}

	public function onSkillCommandRegister(McMMOSkillCommand $command) : void{
		$command->registerCommandWildcard("{GIGA_DRILL_BREAKER_LENGTH}", function(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string{
			/** @var GigaDrillBreaker $giga_drill_breaker */
			$giga_drill_breaker = SubSkillManager::get(SubSkillIds::GIGA_DRILL_BREAKER);
			return number_format($giga_drill_breaker->getLevelIncrease($mcmmo_player->getSkill($this)->getExperience()->getLevel()));
		});

		$compatible_materials = [];
		foreach(array_keys($this->block_xp) as $identifier){
			$compatible_materials[] = $this->block_mapping[$identifier]->getName();
		}
		$compatible_materials = array_unique($compatible_materials);
		sort($compatible_materials);
		$compatible_materials = implode(", ", $compatible_materials);
		$command->registerGuideWildcard("{COMPATIBLE_MATERIALS}", static function(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) use($compatible_materials) : string{
			return $compatible_materials;
		});
	}
}