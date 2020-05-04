<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\subskill;

use cosmicpe\mcmmo\skill\Identifiable;

interface SubSkill extends Identifiable{

	public function getParentIdentifier() : string;

	public function getCooldown() : int;
}