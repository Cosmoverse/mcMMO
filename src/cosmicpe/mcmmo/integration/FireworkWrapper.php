<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\integration;

use pocketmine\world\Position;

interface FireworkWrapper{

	public const COLOR_RED = 0;
	public const COLOR_GREEN = 1;

	public const TYPE_HUGE_SPHERE = 0;

	/**
	 * Spawn a firework at a specific position of a specific
	 * color and type.
	 *
	 * @param Position $position
	 * @param int $type
	 * @param int $color
	 */
	public function spawn(Position $position, int $type, int $color) : void;
}