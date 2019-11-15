<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\combat;

interface CombatSubSkillIds{

	public const DODGE = CombatSkillIds::ACROBATICS . "_dodge";
	public const ROLL = CombatSkillIds::ACROBATICS . "_roll";
}