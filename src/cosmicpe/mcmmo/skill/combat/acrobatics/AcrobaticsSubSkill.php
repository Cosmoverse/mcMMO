<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat\acrobatics;

use cosmicpe\mcmmo\skill\combat\CombatSkillIds;
use cosmicpe\mcmmo\skill\combat\CombatSubSkill;

abstract class AcrobaticsSubSkill implements CombatSubSkill{

	final public function getParentIdentifier() : string{
		return CombatSkillIds::ACROBATICS;
	}
}