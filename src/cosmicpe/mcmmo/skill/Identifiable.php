<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill;

interface Identifiable{

	/**
	 * Conventionally formatted: "your_plugin_name:this->getName()"
	 * @return string
	 */
	public function getIdentifier() : string;

	public function getName() : string;
}