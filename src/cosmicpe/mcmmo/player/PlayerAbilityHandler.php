<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player;

use Closure;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\task\AbilityExpireTask;
use cosmicpe\mcmmo\skill\ability\Ability;
use cosmicpe\mcmmo\skill\ability\AbilityRemoveHandler;
use pocketmine\scheduler\TaskScheduler;

final class PlayerAbilityHandler{

	/** @var TaskScheduler */
	private static $scheduler;

	public static function init(McMMO $plugin) : void{
		self::$scheduler = $plugin->getScheduler();
	}

	/** @var McMMOPlayer */
	private $mcmmo_player;

	/** @var int|null */
	private $ability_task_id;

	/** @var Ability|null */
	private $ability;

	public function __construct(McMMOPlayer $mcmmo_player){
		$this->mcmmo_player = $mcmmo_player;
	}

	public function getCurrent() : ?Ability{
		return $this->ability;
	}

	public function removeCurrent() : void{
		if($this->ability !== null){
			self::$scheduler->cancelTask($this->ability_task_id);
			if($this->ability instanceof AbilityRemoveHandler){
				$this->ability->handleRemove($this->mcmmo_player);
			}
			$this->ability = null;
			$this->ability_task_id = null;
		}
	}

	public function setCurrent(Ability $ability, int $duration, ?Closure $on_expire = null) : void{
		$this->removeCurrent();
		$this->ability = $ability;
		$this->ability_task_id = self::$scheduler->scheduleDelayedTask(new AbilityExpireTask($this, $on_expire), $duration * 20)->getTaskId();
	}
}