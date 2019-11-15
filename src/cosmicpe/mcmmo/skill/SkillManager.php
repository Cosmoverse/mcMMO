<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\PlayerManager;
use cosmicpe\mcmmo\skill\combat\acrobatics\Acrobatics;
use cosmicpe\mcmmo\skill\gathering\excavation\Excavation;
use cosmicpe\mcmmo\skill\listener\McMMOSkillListener;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use pocketmine\plugin\Plugin;

final class SkillManager{

	/** @var PlayerManager */
	private static $player_manager;

	/** @var Skill[] */
	private static $skills = [];

	public static function init(McMMO $plugin) : void{
		self::$player_manager = $plugin->getPlayerManager();
		McMMOSkillListener::init($plugin);

		self::registerDefaults($plugin);
	}

	private static function registerDefaults(McMMO $plugin) : void{
		$plugin->saveResource("skills.yml");

		SubSkillManager::init($plugin);
		$config = yaml_parse_file($plugin->getDataFolder() . "skills.yml");

		self::register($plugin, new Acrobatics());

		["experiences" => $experiences] = $config["excavation"];
		self::register($plugin, new Excavation($experiences));
	}

	public static function register(Plugin $plugin, Skill $skill) : void{
		self::$skills[$skill->getIdentifier()] = $skill;
		if($skill instanceof Listenable){
			$plugin_manager = $plugin->getServer()->getPluginManager();
			foreach($skill->getListeners() as $listener){
				$plugin_manager->registerEvents($listener, $plugin);
			}
		}
	}

	public static function get(string $identifier) : Skill{
		return self::$skills[$identifier];
	}
}