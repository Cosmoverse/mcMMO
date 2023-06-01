<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\vanilla;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static Item NAME_TAG()
 */
final class ExtraVanillaItems{
	use CloningRegistryTrait;

	private function __construct(){
	}

	protected static function register(string $name, Item $item) : void{
		self::_registryRegister($name, $item);
	}

	/**
	 * @return Item[]
	 * @phpstan-return array<string, Item>
	 */
	public static function getAll() : array{
		//phpstan doesn't support generic traits yet :(
		/** @var Item[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup() : void{
		self::register("name_tag", new Item(new ItemIdentifier(ItemTypeIds::newId()), "Name Tag"));
	}
}