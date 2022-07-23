<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\subskill;

use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\skill\combat\acrobatics\Dodge;
use cosmicpe\mcmmo\skill\combat\acrobatics\Roll;
use cosmicpe\mcmmo\skill\gathering\excavation\Archaeology;
use cosmicpe\mcmmo\skill\gathering\excavation\GigaDrillBreaker;

final class SubSkillManager{

	/** @var array<string, SubSkill> */
	private static array $sub_skills = [];

	public static function init(McMMO $plugin) : void{
		self::registerDefaults($plugin);
	}

	private static function registerDefaults(McMMO $plugin) : void{
		$config = yaml_parse_file($plugin->getDataFolder() . "skills.yml");

		["dodge" => $dodge, "roll" => $roll] = $config["acrobatics"]["subskills"];
		self::register(new Dodge($dodge["min-level"], $dodge["max-level"], $dodge["max-chance"], $dodge["damage-amplification"], $dodge["disallowed-causes"]));
		self::register(new Roll($roll["max-level"], $roll["max-chance"], $roll["damage-reduction"]));

		["subskills" => $subskills] = $config["excavation"];
		self::register(new Archaeology($subskills["archaeology"]));
		self::register(new GigaDrillBreaker($subskills["giga-drill-breaker"]));
	}

	public static function register(SubSkill $skill) : void{
		self::$sub_skills[$skill->getIdentifier()] = $skill;
	}

	public static function get(string $identifier) : SubSkill{
		return self::$sub_skills[$identifier];
	}
}