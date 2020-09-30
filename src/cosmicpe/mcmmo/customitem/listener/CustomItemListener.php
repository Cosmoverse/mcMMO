<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\customitem\listener;

use cosmicpe\mcmmo\customitem\CustomItemFactory;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;

class CustomItemListener implements Listener{

	/**
	 * @param PlayerInteractEvent $event
	 * @priority NORMAL
	 */
	public function onPlayerInteract(PlayerInteractEvent $event) : void{
		$item = $event->getItem();
		$custom_item = CustomItemFactory::from($item);
		if($custom_item instanceof Interactable){
			$player = $event->getPlayer();
			if($custom_item->onInteract($player, $item, $event->getBlock())){
				$player->getInventory()->setItemInHand($item);
				$event->cancel();
			}
		}
	}

	/**
	 * @param InventoryTransactionEvent $event
	 * @priority NORMAL
	 */
	public function onInventoryTransaction(InventoryTransactionEvent $event) : void{
		$transaction = $event->getTransaction();
		foreach($transaction->getActions() as $action){
			if($action instanceof SlotChangeAction){
				$item = $action->getSourceItem();
				$custom_item = CustomItemFactory::from($item);
				if($custom_item instanceof Movable){
					$cancel = $custom_item->onMoveItem($transaction->getSource(), $item);
					$action->getInventory()->setItem($action->getSlot(), $item);
					if($cancel){
						$event->cancel();
						break;
					}
				}
			}
		}
	}

	public function onPlayerDropItem(PlayerDropItemEvent $event) : void{
		$item = $event->getItem();
		$custom_item = CustomItemFactory::from($item);
		if($custom_item instanceof Movable && $custom_item->onMoveItem($event->getPlayer(), $item)){
			$event->cancel();
		}
	}
}