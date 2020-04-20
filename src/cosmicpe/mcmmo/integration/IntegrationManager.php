<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\integration;

use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\utils\TypedConfigWrapper;
use InvalidArgumentException;
use pocketmine\utils\Config;

final class IntegrationManager{

	/** @var FireworkWrapper */
	private $firework;

	public function __construct(McMMO $plugin){
		$plugin->saveResource("integration.yml");

		$config = new TypedConfigWrapper(new Config($plugin->getDataFolder() . "integration.yml"));
		switch($fireworks_plugin = $config->getString("firework")){
			case "BlockHorizons_Fireworks":
				$this->setFireworkWrapper(new BlockHorizonsFireworkWrapper());
				break;
			case "custom":
				break;
			case "none":
				$this->setFireworkWrapper(new EmptyFireworkWrapper());
				break;
			default:
				throw new InvalidArgumentException("Unsupported fireworks plugin " . $fireworks_plugin);
		}
	}

	public function init() : void{
		if($this->firework === null){
			throw new InvalidArgumentException("Firework custom plugin not specified. Please specify your custom firework plugin during PluginBase::onLoad()");
		}
	}

	public function getFireworkWrapper() : FireworkWrapper{
		return $this->firework;
	}

	public function setFireworkWrapper(FireworkWrapper $firework) : void{
		$this->firework = $firework;
	}
}