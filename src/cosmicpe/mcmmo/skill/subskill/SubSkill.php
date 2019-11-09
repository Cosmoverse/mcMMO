<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\subskill;

abstract class SubSkill{

	/** @var int */
	protected $max_level;

	public function __construct(int $max_level){
		$this->max_level = $max_level;
	}

	public function getMaxLevel() : int{
		return $this->max_level;
	}
}