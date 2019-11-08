<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

interface Skill{

	/**
	 * Conventionally formatted: "your_plugin_name:this->getName()"
	 * @return string
	 */
	public function getIdentifier() : string;

	public function getName() : string;
}