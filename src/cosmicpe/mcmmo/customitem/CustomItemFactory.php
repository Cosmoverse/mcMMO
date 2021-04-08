<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\customitem;

use cosmicpe\mcmmo\customitem\listener\CustomItemListener;
use cosmicpe\mcmmo\McMMO;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Utils;

final class CustomItemFactory{

	private const TAG_CUSTOM_ITEM = "mcmmo:custom_item";
	public const TAG_CUSTOM_ITEM_IDENTIFIER = "identifier";

	/**
	 * @var CustomItem[]|string[]
	 * @phpstan-var class-string<CustomItem>[]
	 */
	private static array $custom_items = [];

	public static function load(McMMO $plugin) : void{
		self::registerDefaults();
	}

	public static function init(McMMO $plugin) : void{
		$plugin->getServer()->getPluginManager()->registerEvents(new CustomItemListener(), $plugin);
	}

	private static function registerDefaults() : void{
		self::register(GigaDrillShovel::class);
	}

	public static function register(string $class) : void{
		Utils::testValidInstance($class, CustomItem::class);
		/** @var CustomItem|string $class */
		self::$custom_items[$class::getIdentifier()] = $class;
	}

	/**
	 * @param string $identifier
	 * @param mixed ...$args
	 * @return Item
	 */
	public static function get(string $identifier, ...$args) : Item{
		/** @var CustomItem $custom_item */
		$custom_item = new self::$custom_items[$identifier](...$args);
		$item = $custom_item->getItem();
		$item->getNamedTag()->setTag(self::TAG_CUSTOM_ITEM, $custom_item->nbtSerialize());
		return $item;
	}

	public static function from(Item $item) : ?CustomItem{
		/** @var CompoundTag|null $tag */
		$tag = $item->getNamedTag()->getTag(self::TAG_CUSTOM_ITEM);
		return $tag !== null ? self::$custom_items[$tag->getString(self::TAG_CUSTOM_ITEM_IDENTIFIER)]::from($item, $tag) : null;
	}

	public static function clean(Item $item) : void{
		$item->getNamedTag()->removeTag(self::TAG_CUSTOM_ITEM);
	}
}