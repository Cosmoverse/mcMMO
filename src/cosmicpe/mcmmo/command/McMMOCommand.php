<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\command;

use cosmicpe\mcmmo\McMMO;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class McMMOCommand extends Command implements PluginOwned{

	/** @var McMMO */
	protected $plugin;

	public function __construct(McMMO $plugin, string $name, string $description = "", ?string $usageMessage = null, array $aliases = []){
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->plugin = $plugin;
	}

	final public function getOwningPlugin() : Plugin{
		return $this->plugin;
	}

	final public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($this->testPermission($sender)){
			$this->onExecute($sender, $commandLabel, $args);
		}
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param string[] $args
	 */
	abstract protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void;
}