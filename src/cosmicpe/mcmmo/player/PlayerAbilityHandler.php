<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player;

use Closure;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\task\AbilityExpireTask;
use cosmicpe\mcmmo\skill\ability\Ability;
use cosmicpe\mcmmo\skill\ability\AbilityRemoveHandler;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;

final class PlayerAbilityHandler{

	private static TaskScheduler $scheduler;

	public static function init(McMMO $plugin) : void{
		self::$scheduler = $plugin->getScheduler();
	}

	private ?McMMOPlayer $mcmmo_player;
	private ?TaskHandler $ability_task_handler = null;
	private ?Ability $ability = null;

	public function __construct(McMMOPlayer $mcmmo_player){
		$this->mcmmo_player = $mcmmo_player;
	}

	public function destroy() : void{
		$this->removeCurrent();
		$this->mcmmo_player = null;
	}

	public function getCurrent() : ?Ability{
		return $this->ability;
	}

	public function removeCurrent() : void{
		if($this->ability !== null){
			if(!$this->ability_task_handler->isCancelled()){
				$this->ability_task_handler->cancel();
			}
			$this->ability_task_handler = null;

			if($this->ability instanceof AbilityRemoveHandler){
				$this->ability->onRemove($this->mcmmo_player);
			}
			$this->ability = null;
		}
	}

	/**
	 * @param Ability $ability
	 * @param int $duration
	 * @param (Closure() : void)|null $on_expire
	 */
	public function setCurrent(Ability $ability, int $duration, ?Closure $on_expire = null) : void{
		$this->removeCurrent();
		$this->ability = $ability;
		$this->ability_task_handler = self::$scheduler->scheduleDelayedTask(new AbilityExpireTask($this, $on_expire), $duration * 20);
	}
}