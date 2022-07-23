<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\sound;

use InvalidArgumentException;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\world\sound\Sound;

class McMMOLevelUpSound implements Sound{

	public function __construct(
		protected int $volume = 320
	){}

	public function encode(?Vector3 $pos) : array{
		if($pos === null){
			throw new InvalidArgumentException("No position provided for sound.");
		}

		$packet = new PlaySoundPacket();
		$packet->soundName = "random.levelup";
		$packet->x = $pos->x;
		$packet->y = $pos->y;
		$packet->z = $pos->z;
		$packet->volume = $this->volume;
		$packet->pitch = 0.5;
		return [$packet];
	}
}