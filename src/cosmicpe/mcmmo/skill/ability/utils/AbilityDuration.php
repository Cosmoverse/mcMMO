<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\ability\utils;

use InvalidArgumentException;

final class AbilityDuration{

	public static function parse(array $config) : AbilityDuration{
		return new AbilityDuration($config["base"], $config["increase"]["per"], $config["increase"]["by"], $config["max"] ?? -1);
	}

	/** @var int */
	private $base;

	/** @var int */
	private $increase_per;

	/** @var int */
	private $increase_by;

	/** @var int */
	private $cap;

	public function __construct(int $base, int $increase_per, int $increase_by, int $cap){
		if($base < 0){
			throw new InvalidArgumentException("base cannot be < 0, got " . $base);
		}
		if($increase_per < 0){
			throw new InvalidArgumentException("increase_per cannot be < 0, got " . $increase_per);
		}
		if($increase_by < 0){
			throw new InvalidArgumentException("increase_by cannot be < 0, got " . $increase_by);
		}

		$this->base = $base;
		$this->increase_per = $increase_per;
		$this->increase_by = $increase_by;
		$this->cap = $cap < 0 ? PHP_INT_MAX : $cap;
	}

	public function get(int $level) : int{
		return min($this->base + (((int) floor($this->increase_per / $level)) * $this->increase_by), $this->cap);
	}
}