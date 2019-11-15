<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\ability;

use cosmicpe\mcmmo\skill\subskill\SubSkill;

interface BuffableAbilitySubSkill extends SubSkill{

	public function getLevelIncrease(int $level) : int;
}