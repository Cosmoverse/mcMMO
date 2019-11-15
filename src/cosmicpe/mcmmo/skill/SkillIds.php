<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

use cosmicpe\mcmmo\skill\combat\CombatSkillIds;
use cosmicpe\mcmmo\skill\gathering\GatheringSkillIds;

interface SkillIds extends CombatSkillIds, GatheringSkillIds{
}