<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\gathering\excavation;

use cosmicpe\mcmmo\skill\gathering\GatheringSkillIds;
use cosmicpe\mcmmo\skill\gathering\GatheringSubSkill;

abstract class ArchaeologySubSkill implements GatheringSubSkill{

	final public function getParentIdentifier() : string{
		return GatheringSkillIds::EXCAVATION;
	}
}