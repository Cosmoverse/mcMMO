<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\world\sound\Sound;

class McMMOLevelUpSound implements Sound{

	public function __construct(
		protected int $volume = 320
	){}

	public function encode(Vector3 $pos) : array{
		return [
			PlaySoundPacket::create("random.levelup", $pos->x, $pos->y, $pos->z, $this->volume, 0.5)
		];
	}
}