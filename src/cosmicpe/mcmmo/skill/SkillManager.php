<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use Closure;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\PlayerManager;
use cosmicpe\mcmmo\skill\combat\acrobatics\Acrobatics;
use cosmicpe\mcmmo\skill\gathering\excavation\Excavation;
use cosmicpe\mcmmo\skill\listener\McMMOSkillListener;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

final class SkillManager{

	private static bool $init = false;
	private static PlayerManager $player_manager;

	/** @var Skill[] */
	private static array $skills = [];

	/** @var array<Closure() : void> */
	private static array $on_init = [];

	public static function load(McMMO $plugin) : void{
		self::registerDefaults($plugin);
	}

	public static function init(McMMO $plugin) : void{
		self::$player_manager = $plugin->getPlayerManager();
		McMMOSkillListener::init($plugin);

		self::$init = true;
		foreach(self::$on_init as $cb){
			$cb();
		}
		self::$on_init = [];
	}

	private static function onInit(Closure $callback) : void{
		if(self::$init){
			$callback();
		}else{
			self::$on_init[] = $callback;
		}
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
			self::onInit(static function() use($skill, $plugin) : void{
				$plugin_manager = Server::getInstance()->getPluginManager();
				foreach($skill->getListeners() as $listener){
					$plugin_manager->registerEvents($listener, $plugin);
				}
			});
		}
	}

	public static function get(string $identifier) : Skill{
		return self::$skills[$identifier];
	}

	public static function getNullable(string $identifier) : ?Skill{
		return self::$skills[$identifier] ?? null;
	}

	/**
	 * Returns all skills indexed by their identifier.
	 *
	 * @return Skill[]
	 */
	public static function getAll() : array{
		return self::$skills;
	}
}