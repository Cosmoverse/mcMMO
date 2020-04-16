<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\command;

use cosmicpe\mcmmo\McMMO;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;

abstract class McMMOCommand extends Command implements PluginIdentifiableCommand{

	/** @var McMMO */
	protected $plugin;

	public function __construct(McMMO $plugin, string $name, string $description = "", ?string $usageMessage = null, array $aliases = []){
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->plugin = $plugin;
	}

	final public function getPlugin() : Plugin{
		return $this->plugin;
	}

	final public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($this->testPermission($sender)){
			$this->onExecute($sender, $commandLabel, $args);
		}
	}

	abstract protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void;
}