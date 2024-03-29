<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\command;

use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\experience\SkillExperienceManager;
use cosmicpe\mcmmo\skill\SkillManager;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

final class McMMOCommandManager{

	public static function init(McMMO $plugin) : void{
		// Let other plugins register their custom skills during PluginBase::onEnable()
		$plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(static function() use($plugin) : void{
			self::registerConfiguredCommands($plugin);
		}), 1);
	}

	private static function registerConfiguredCommands(McMMO $plugin) : void{
		$plugin->saveResource("commands.yml");
		$config = yaml_parse_file($plugin->getDataFolder() . "commands.yml");

		$commands = [];

		foreach($config["skill_commands"] as $command_name => [
			"skill" => $skill_identifier,
			"command" => $command_unparsed,
			"guide" => $guide_unparsed
		]){
			$skill = SkillManager::get($skill_identifier);
			$command_config = implode(TextFormat::RESET . TextFormat::EOL, array_map(TextFormat::class . "::colorize", $command_unparsed));
			$guide_config = array_map(static fn(array $strings) : string => implode(TextFormat::RESET . TextFormat::EOL, array_map(TextFormat::class . "::colorize", $strings)), $guide_unparsed);

			$command = new McMMOSkillCommand($plugin, $skill, $command_name, $command_config, $guide_config);

			$command->registerGuideWildcard("{SKILL}", static fn(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string => $command->getSkill()->getName());
			$command->registerGuideWildcard("{PAGE}", static fn(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string => $args[1]);
			$command->registerGuideWildcard("{PAGES}", static fn(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string => (string) count($command->getGuideConfig()));

			$command->registerCommandWildcard("{SKILL}", static fn(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string => $command->getSkill()->getName());
			$command->registerCommandWildcard("{LEVEL}", static fn(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string => number_format($mcmmo_player->getSkill($command->getSkill())->getExperience()->getLevel()));
			$command->registerCommandWildcard("{TOTAL_XP}", static fn(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string => number_format($mcmmo_player->getSkill($command->getSkill())->getExperience()->getValue()));
			$command->registerCommandWildcard("{LEVEL_MAX_XP}", static fn(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string => number_format(SkillExperienceManager::get()->getExperienceFromLevel($mcmmo_player->getSkill($command->getSkill())->getExperience()->getLevel() + 1)));
			$command->registerCommandWildcard("{COMMAND}", static fn(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string => $command->getName());

			$command->getSkill()->onSkillCommandRegister($command);
			$commands[] = $command;
		}

		$plugin->getServer()->getCommandMap()->registerAll($plugin->getName(), $commands);
	}
}