<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use ArrayObject;
use Closure;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\PlayerManager;
use pocketmine\event\Cancellable;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Event;
use pocketmine\player\Player;
use ReflectionException;
use ReflectionFunction;
use RuntimeException;

final class McMMOSkillListener{

	/** @var McMMO */
	private static $plugin;

	/** @var ArrayObject<Closure>[][] */
	private static $callbacks = [];

	/** @var Closure[] */
	private static $parser = [];

	public static function init(McMMO $plugin) : void{
		self::$plugin = $plugin;

		self::registerParser(EntityDamageEvent::class, static function(EntityDamageEvent $event, PlayerManager $manager) : ?McMMOListenerParserResult{
			$player = $event->getEntity();
			if($player instanceof Player){
				$mcmmo_player = $manager->get($player);
				if($mcmmo_player !== null){
					return new McMMOListenerParserResult($player, $mcmmo_player);
				}
			}
			return null;
		});
	}

	public static function registerEvent(int $priority, Closure $callback) : void{
		$event_class = null;
		try{
			$event_class = (new ReflectionFunction($callback))->getParameters()[0]->getClass()->getName();
		}catch(ReflectionException $e){
			throw new RuntimeException($e);
		}

		if(!isset(self::$callbacks[$priority][$event_class])){
			self::$callbacks[$priority][$event_class] = $events = new ArrayObject();
			try{
				$manager = self::$plugin->getPlayerManager();
				self::$plugin->getServer()->getPluginManager()->registerEvent($event_class, static function(Event $event) use ($events, $event_class, $manager) : void{
					/** @var McMMOListenerParserResult $result */
					$result = (McMMOSkillListener::$parser[$event_class])($event, $manager);
					if($result !== null){
						foreach($events as $ev){
							$ev($event, $result->player, $result->mcmmo_player);
							if($event instanceof Cancellable && $event->isCancelled()){
								break;
							}
						}
					}
				}, $priority, self::$plugin);
			}catch(ReflectionException $e){
				throw new RuntimeException($e);
			}
		}

		self::$callbacks[$priority][$event_class]->append($callback);
	}

	public static function registerParser(string $event, Closure $parser) : void{
		self::$parser[$event] = $parser;
	}
}