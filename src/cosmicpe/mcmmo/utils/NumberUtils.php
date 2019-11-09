<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\utils;

final class NumberUtils{

	public static function getRandomBool(float $percentage) : bool{
		return (mt_rand() / mt_getrandmax()) <= ($percentage * 0.01);
	}
}