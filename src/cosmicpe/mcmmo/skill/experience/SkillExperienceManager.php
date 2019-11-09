<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\experience;

final class SkillExperienceManager{

	/** @var SkillExperience */
	private static $experience;

	public static function set(SkillExperience $experience) : void{
		self::$experience = $experience;
	}

	public static function get() : SkillExperience{
		return self::$experience;
	}
}