<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\experience;

interface SkillExperience{

	/**
	 * This returns the experience at which this $level
	 * starts. Eg: if level 2 starts at 30 experience,
	 * getExperienceFromLevel(2) will return 30.
	 *
	 * @param int $level
	 * @return int
	 */
	public function getExperienceFromLevel(int $level) : int;

	/**
	 * This returns the level corresponding to this experience
	 * value. This can be derived from getExperienceFromLevel()
	 * algebraically (you may have to (int) ceil(the value)).
	 *
	 * @param int $experience
	 * @return int
	 */
	public function getLevelFromExperience(int $experience) : int;
}