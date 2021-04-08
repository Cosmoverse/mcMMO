<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\command;

use Closure;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\Skill;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;

class McMMOSkillCommand extends McMMOCommand{

	private string $command_config;
	private Skill $skill;

	/** @var string[] */
	private array $guide_config;

	/** @var Closure[] */
	private array $command_wildcards = [];

	/** @var Closure[] */
	private array $guide_wildcards = [];

	/**
	 * @param McMMO $plugin
	 * @param Skill $skill
	 * @param string $name
	 * @param string $command_config
	 * @param string[] $guide_config
	 */
	public function __construct(McMMO $plugin, Skill $skill, string $name, string $command_config, array $guide_config){
		parent::__construct($plugin, $name);
		$this->command_config = $command_config;
		$this->guide_config = $guide_config;
		$this->skill = $skill;
	}

	public function getSkill() : Skill{
		return $this->skill;
	}

	public function getCommandConfig() : string{
		return $this->command_config;
	}

	/**
	 * @return string[]
	 */
	public function getGuideConfig() : array{
		return $this->guide_config;
	}

	public function registerCommandWildcard(string $wildcard, Closure $resolution) : void{
		Utils::validateCallableSignature(static function(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string{ return ""; }, $resolution);
		$this->command_wildcards[$wildcard] = $resolution;
	}

	public function registerGuideWildcard(string $wildcard, Closure $resolution) : void{
		Utils::validateCallableSignature(static function(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : string{ return ""; }, $resolution);
		$this->guide_wildcards[$wildcard] = $resolution;
	}

	/**
	 * @param Player $sender
	 * @param McMMOPlayer $mcmmo_player
	 * @param string $commandLabel
	 * @param string[] $args
	 * @return string[]
	 */
	private function getCommandWildcards(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : array{
		$translation = [];
		foreach($this->command_wildcards as $wildcard => $resolution){
			$translation[$wildcard] = $resolution($sender, $mcmmo_player, $commandLabel, $args);
		}
		return $translation;
	}

	/**
	 * @param Player $sender
	 * @param McMMOPlayer $mcmmo_player
	 * @param string $commandLabel
	 * @param string[] $args
	 * @return string[]
	 */
	private function getGuideWildcards(Player $sender, McMMOPlayer $mcmmo_player, string $commandLabel, array $args) : array{
		$translation = [];
		foreach($this->guide_wildcards as $wildcard => $resolution){
			$translation[$wildcard] = $resolution($sender, $mcmmo_player, $commandLabel, $args);
		}
		return $translation;
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!($sender instanceof Player)){
			$sender->sendMessage("This command does not support console usage.");
			return;
		}

		$mcmmo_player = $this->plugin->getPlayerManager()->get($sender);
		if($mcmmo_player === null){
			$sender->sendMessage(TextFormat::RED . "Please wait while your mcMMO player data loads.");
			return;
		}

		if(isset($args[0])){
			if($args[0] === "?"){
				$page = (int) ($args[1] ?? 1);
				$args[1] = (string) $page;
				$index = $page - 1;
				if(isset($this->guide_config[$index])){
					$sender->sendMessage(strtr($this->guide_config[$index], $this->getGuideWildcards($sender, $mcmmo_player, $commandLabel, $args)));
				}else{
					$sender->sendMessage("That page does not exist, there are only " . count($this->guide_config) . " total pages.");
				}
				return;
			}

			$sender->sendMessage(
				TextFormat::RED . "Proper usage is /{$commandLabel}" . TextFormat::EOL .
				TextFormat::RED . "Proper usage is /{$commandLabel} ? [page]"
			);
			return;
		}

		$sender->sendMessage(strtr($this->command_config, $this->getCommandWildcards($sender, $mcmmo_player, $commandLabel, $args)));
	}
}