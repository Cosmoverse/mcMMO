<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player;

use cosmicpe\mcmmo\database\IDatabase;
use cosmicpe\mcmmo\McMMO;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use Ramsey\Uuid\UuidInterface;

final class PlayerManager{

	/** @var McMMOPlayer[] */
	private array $players = [];

	private IDatabase $database;

	public function init(McMMO $plugin, IDatabase $database) : void{
		$plugin_manager = $plugin->getServer()->getPluginManager();

		/** @noinspection PhpUnhandledExceptionInspection */
		$plugin_manager->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event) : void{
			$this->load($event->getPlayer()->getUniqueId());
		}, EventPriority::MONITOR, $plugin);

		/** @noinspection PhpUnhandledExceptionInspection */
		$plugin_manager->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $event) : void{
			$player = $this->get($event->getPlayer());
			if($player !== null){
				$this->unload($player);
			}
		}, EventPriority::MONITOR, $plugin);
		$this->database = $database;

		PlayerAbilityHandler::init($plugin);
	}

	public function destroy() : void{
		foreach($this->players as $player){
			$this->unload($player);
		}
	}

	public function load(UuidInterface $uuid) : void{
		$this->database->load($uuid, function(McMMOPlayer $player) : void{
			$this->players[$player->getUniqueId()->getBytes()] = $player;
		});
	}

	public function get(Player $player) : ?McMMOPlayer{
		return $this->getByUUID($player->getUniqueId());
	}

	public function getByUUID(UuidInterface $uuid) : ?McMMOPlayer{
		return $this->players[$uuid->getBytes()] ?? null;
	}

	public function unload(McMMOPlayer $player) : void{
		$player->onDisconnect();
		$this->database->save($player);
		unset($this->players[$player->getUniqueId()->getBytes()]);
	}
}