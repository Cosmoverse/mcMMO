<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player;

use cosmicpe\mcmmo\database\IDatabase;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\McMMOPlayer as McMMOPlayer;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\utils\UUID;

final class PlayerManager{

	/** @var McMMOPlayer[] */
	private $players = [];

	/** @var IDatabase */
	private $database;

	public function __construct(McMMO $plugin, IDatabase $database){
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
	}

	public function load(UUID $uuid) : void{
		$this->database->load($uuid, function(McMMOPlayer $player) : void{
			$this->players[$player->getUniqueId()->toBinary()] = $player;
		});
	}

	public function get(Player $player) : ?McMMOPlayer{
		return $this->getByUUID($player->getUniqueId());
	}

	public function getByUUID(UUID $uuid) : ?McMMOPlayer{
		return $this->players[$uuid->toBinary()] ?? null;
	}

	public function unload(McMMOPlayer $player) : void{
		$this->database->save($player);
		unset($this->players[$player->getUniqueId()->toBinary()]);
	}
}