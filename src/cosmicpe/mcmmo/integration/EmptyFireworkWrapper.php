<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\integration;

use pocketmine\world\Position;

final class EmptyFireworkWrapper implements FireworkWrapper{

	public function spawn(Position $position, int $type, int $color) : void{
	}
}