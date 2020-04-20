<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\integration;

use BlockHorizons\Fireworks\entity\FireworksRocket;
use BlockHorizons\Fireworks\item\Fireworks;
use pocketmine\entity\EntityFactory;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\world\Position;

final class BlockHorizonsFireworkWrapper implements FireworkWrapper{

	private const COLOR_MAPPING = [
		self::COLOR_RED => Fireworks::COLOR_RED,
		self::COLOR_GREEN => Fireworks::COLOR_DARK_GREEN
	];

	private const TYPE_MAPPING = [
		self::TYPE_HUGE_SPHERE => Fireworks::TYPE_HUGE_SPHERE
	];

	public function spawn(Position $position, int $type, int $color) : void{
		/** @var Fireworks $fw */
		$fw = ItemFactory::get(ItemIds::FIREWORKS);
		$fw->addExplosion(self::TYPE_MAPPING[$type], self::COLOR_MAPPING[$color], "", false, false);
		$fw->setFlightDuration(0);

		$nbt = EntityFactory::createBaseNBT($position->asVector3(), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
		EntityFactory::create(FireworksRocket::class, $position->getWorld(), $nbt, $fw)->spawnToAll();
	}
}