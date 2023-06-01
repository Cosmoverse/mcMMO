<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\vanilla;

use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\scheduler\AsyncPool;
use pocketmine\scheduler\AsyncTask;
use pocketmine\world\format\io\GlobalItemDataHandlers;

final class ExtraVanillaData{

	public static function registerOnAllThreads(AsyncPool $pool) : void{
		self::registerOnCurrentThread();
		$pool->addWorkerStartHook(function(int $worker) use($pool) : void{
			$pool->submitTaskToWorker(new class extends AsyncTask{
				public function onRun() : void{
					ExtraVanillaData::registerOnCurrentThread();
				}
			}, $worker);
		});
	}

	public static function registerOnCurrentThread() : void{
		self::registerItems();
	}

	private static function registerItems() : void{
		self::registerSimpleItem(ItemTypeNames::NAME_TAG, ExtraVanillaItems::NAME_TAG(), ["name_tag"]);
	}

	/**
	 * @param string[] $stringToItemParserNames
	 */
	private static function registerSimpleItem(string $id, Item $item, array $stringToItemParserNames) : void{
		GlobalItemDataHandlers::getDeserializer()->map($id, fn() => clone $item);
		GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData($id));

		foreach($stringToItemParserNames as $name){
			StringToItemParser::getInstance()->register($name, fn() => clone $item);
		}
	}
}