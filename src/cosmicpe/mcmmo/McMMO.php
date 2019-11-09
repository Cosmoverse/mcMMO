<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo;

use cosmicpe\mcmmo\database\IDatabase;
use cosmicpe\mcmmo\database\sqlite\SQLiteDatabase;
use cosmicpe\mcmmo\player\PlayerManager;
use cosmicpe\mcmmo\skill\experience\ExponentialSkillExperience;
use cosmicpe\mcmmo\skill\experience\SkillExperienceManager;
use cosmicpe\mcmmo\skill\SkillManager;
use InvalidArgumentException;
use pocketmine\plugin\PluginBase;

final class McMMO extends PluginBase{

	/** @var McMMO */
	private static $instance;

	public static function getInstance() : McMMO{
		return self::$instance;
	}

	/** @var IDatabase */
	private $database;

	/** @var PlayerManager */
	private $player_manager;

	protected function onEnable() : void{
		self::$instance = $this;

		$this->parseExperienceFormula();

		$this->database = new SQLiteDatabase($this);
		$this->player_manager = new PlayerManager($this, $this->database);

		SkillManager::init($this);
	}

	private function parseExperienceFormula() : void{
		$config = $this->getConfig()->get("experience");
		switch($type = strtolower($config["type"])){
			case "exponential":
				$args = $config[$type];
				SkillExperienceManager::set(new ExponentialSkillExperience($args["base"], $args["multiplier"], $args["exponent"]));
				break;
			default:
				throw new InvalidArgumentException("Invalid experience type \"" . $config["type"] . " in config.yml");
		}
	}

	public function getPlayerManager() : PlayerManager{
		return $this->player_manager;
	}

	protected function onDisable() : void{
		$this->database->close();
	}
}