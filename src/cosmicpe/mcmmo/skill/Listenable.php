<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

interface Listenable{

	/**
	 * @return SkillListener[]
	 */
	public function getListeners() : array;
}