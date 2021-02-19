<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use ArrayObject;
use Closure;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\player\PlayerManager;
use InvalidArgumentException;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;
use RuntimeException;

final class McMMOSkillListener{

	/** @var McMMO */
	private static $plugin;

	/** @var ArrayObject<string, Closure>[][] */
	private static $callbacks = [];

	/** @var Closure[] */
	private static $parser = [];

	/** @var McMMOExperienceToller[] */
	private static $experience_tollers = [];

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

		self::registerParser(BlockBreakEvent::class, static function(BlockBreakEvent $event, PlayerManager $manager) : ?McMMOListenerParserResult{
			$player = $event->getPlayer();
			$mcmmo_player = $manager->get($player);
			return $mcmmo_player !== null ? new McMMOListenerParserResult($player, $mcmmo_player) : null;
		});

		self::registerParser(PlayerItemUseEvent::class, static function(PlayerEvent $event, PlayerManager $manager) : ?McMMOListenerParserResult{
			$player = $event->getPlayer();
			$mcmmo_player = $manager->get($player);
			return $mcmmo_player !== null ? new McMMOListenerParserResult($player, $mcmmo_player) : null;
		});

		McMMOSubSkillListener::init();
	}

	private static function getExperienceToller(Event $event) : McMMOExperienceToller{
		return self::$experience_tollers[$id = spl_object_id($event)] ?? self::$experience_tollers[$id] = new McMMOExperienceToller();
	}

	private static function removeExperienceToller(Event $event, ?McMMOPlayer $player) : void{
		if(isset(self::$experience_tollers[$id = spl_object_id($event)])){
			$toller = self::$experience_tollers[$id];
			unset(self::$experience_tollers[$id]);
			if($player !== null && (!($event instanceof Cancellable) || !$event->isCancelled())){
				$toller->apply($player);
			}
		}
	}

	/**
	 * @param Closure $callback
	 * @param int $priority
	 *
	 * @phpstan-template TEvent of Event
	 * @phpstan-param Closure(TEvent, Player, McMMOPlayer, McMMOExperienceToller) : void $callback
	 */
	public static function registerEvent(int $priority, Closure $callback) : void{
		$event_class = null;

		$type = (new ReflectionFunction($callback))->getParameters()[0]->getType();
		if(!($type instanceof ReflectionNamedType)){
			throw new InvalidArgumentException("Invalid parameter type in supplied callback");
		}
		$event_class = $type->getName();

		if(!isset(self::$callbacks[$priority][$event_class])){
			self::$callbacks[$priority][$event_class] = $events = new ArrayObject();
			try{
				$manager = self::$plugin->getPlayerManager();
				$plugin_manager = self::$plugin->getServer()->getPluginManager();
				$plugin_manager->registerEvent($event_class, static function(Event $event) use ($events, $event_class, $manager) : void{
					/** @var McMMOListenerParserResult $result */
					$result = (McMMOSkillListener::$parser[$event_class])($event, $manager);
					if($result !== null){
						$toller = self::getExperienceToller($event);
						foreach($events as $ev){
							$ev($event, $result->player, $result->mcmmo_player, $toller);
							if($event instanceof Cancellable && $event->isCancelled()){
								break;
							}
						}
					}
				}, $priority, self::$plugin);

				$plugin_manager->registerEvent($event_class, static function(Event $event) use ($event_class, $manager) : void{
					self::removeExperienceToller($event, (McMMOSkillListener::$parser[$event_class])($event, $manager)->mcmmo_player ?? null);
				}, EventPriority::MONITOR, self::$plugin, true);
			}catch(ReflectionException $e){
				throw new RuntimeException($e->getMessage());
			}
		}

		self::$callbacks[$priority][$event_class]->append($callback);
	}

	public static function registerParser(string $event, Closure $parser) : void{
		self::$parser[$event] = $parser;
	}
}