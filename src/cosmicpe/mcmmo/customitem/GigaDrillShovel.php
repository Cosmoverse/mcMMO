<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\customitem;

use cosmicpe\mcmmo\customitem\listener\Interactable;
use cosmicpe\mcmmo\customitem\listener\Movable;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\skill\gathering\excavation\GigaDrillBreakerAbility;
use InvalidArgumentException;
use pocketmine\block\Block;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\Shovel;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

/**
 * GigaDrillShovel custom item is an item that CONTAINS the original shovel's
 * efficiency enchantment level while overriding it with a possibly higher
 * enchantment level.
 * This is a hacky custom item who's only purpose is to increase client-sided
 * mining speed.
 */
class GigaDrillShovel extends CustomItem implements Interactable, Movable{

	private const TAG_BUFF = "Buff";
	private const TAG_ORIGINAL = "Original";

	private const TAG_MARKER_LORE = TextFormat::RESET . TextFormat::YELLOW . "- TEMPORARY EFFICIENCY BUFF -";

	public static function getIdentifier() : string{
		return CustomItemIds::GIGA_DRILL_SHOVEL;
	}

	public static function from(Item $item, CompoundTag $tag) : GigaDrillShovel{
		if(!($item instanceof Shovel)){
			throw new InvalidArgumentException("Expected shovel, got " . get_class($item));
		}
		return new GigaDrillShovel($item, $tag->getShort(self::TAG_BUFF), $tag->getShort(self::TAG_ORIGINAL));
	}

	/** @var Shovel $shovel */
	private $shovel;

	/** @var int */
	private $buff;

	/** @var int */
	private $original;

	public function __construct(Shovel $shovel, int $buff, ?int $original = null){
		$this->shovel = $shovel;
		$this->buff = $buff;
		$this->original = $original ?? $shovel->getEnchantmentLevel(Enchantment::EFFICIENCY());
	}

	public function getItem() : Item{
		$lore = $this->shovel->getLore();
		if(!in_array(self::TAG_MARKER_LORE, $lore, true)){
			$lore[] = self::TAG_MARKER_LORE;
		}
		return $this->shovel->addEnchantment(new EnchantmentInstance(Enchantment::EFFICIENCY(), $this->original + $this->buff))->setLore($lore);
	}

	public function nbtSerialize() : CompoundTag{
		return parent::nbtSerialize()
			->setShort(self::TAG_BUFF, $this->buff)
			->setShort(self::TAG_ORIGINAL, $this->original);
	}

	public function clean(Item $item) : void{
		CustomItemFactory::clean($item);
		$lore = $item->getLore();
		foreach($lore as $k => $line){
			if($line === self::TAG_MARKER_LORE){
				unset($lore[$k]);
				$item->setLore($lore);
				break;
			}
		}
		if($this->original === 0){
			$item->removeEnchantment(Enchantment::EFFICIENCY());
		}else{
			$item->addEnchantment(new EnchantmentInstance(Enchantment::EFFICIENCY(), $this->original));
		}
	}

	public function onInteract(Player $player, Item $item, Block $block) : bool{
		$mcmmo_player = McMMO::getInstance()->getPlayerManager()->get($player);
		if($mcmmo_player === null || !($mcmmo_player->getAbilityHandler()->getCurrent() instanceof GigaDrillBreakerAbility)){
			$this->clean($item);
			return true;
		}
		return false;
	}

	public function onMoveItem(Player $player, Item $item) : bool{
		$this->clean($item);
		return true;
	}
}