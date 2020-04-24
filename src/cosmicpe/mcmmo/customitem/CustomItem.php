<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\customitem;

use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;

abstract class CustomItem{

	abstract public static function getIdentifier() : string;

	/**
	 * @param Item $item
	 * @param CompoundTag $tag
	 * @return static
	 */
	public static function from(Item $item, CompoundTag $tag){
		return new static();
	}

	abstract public function getItem() : Item;

	public function nbtSerialize() : CompoundTag{
		return CompoundTag::create()
			->setString(CustomItemFactory::TAG_CUSTOM_ITEM_IDENTIFIER, static::getIdentifier());
	}
}