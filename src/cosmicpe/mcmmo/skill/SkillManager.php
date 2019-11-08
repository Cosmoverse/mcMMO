<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\PlayerManager;
use cosmicpe\mcmmo\skill\combat\acrobatics\Acrobatics;
use pocketmine\plugin\Plugin;

final class SkillManager{

	/** @var PlayerManager */
	private static $player_manager;

	/** @var Skill[] */
	private static $skills = [];

	public static function init(McMMO $plugin) : void{
		self::$player_manager = $plugin->getPlayerManager();
		self::registerDefaults($plugin);
	}

	private static function registerDefaults(McMMO $plugin) : void{
		self::register($plugin,
			new Acrobatics()
		);
	}

	public static function register(Plugin $plugin, Skill ...$skills) : void{
		foreach($skills as $skill){
			/** @var SkillInstance|string $class */
			self::$skills[$skill->getIdentifier()] = $skill;
			if($skill instanceof Listenable){
				$plugin_manager = $plugin->getServer()->getPluginManager();
				foreach($skill->getListeners() as $listener){
					$plugin_manager->registerEvents(new $listener(self::$player_manager), $plugin);
				}
			}
		}
	}

	public static function get(string $identifier) : Skill{
		return self::$skills[$identifier];
	}
}