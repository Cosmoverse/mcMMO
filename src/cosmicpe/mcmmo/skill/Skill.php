<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use cosmicpe\mcmmo\command\McMMOSkillCommand;

interface Skill extends Identifiable{

	public function onSkillCommandRegister(McMMOSkillCommand $command) : void;
}