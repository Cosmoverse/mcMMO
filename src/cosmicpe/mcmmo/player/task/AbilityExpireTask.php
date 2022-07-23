<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player\task;

use Closure;
use cosmicpe\mcmmo\player\PlayerAbilityHandler;
use pocketmine\scheduler\Task;

class AbilityExpireTask extends Task{

	/**
	 * @param PlayerAbilityHandler $ability_handler
	 * @param (Closure() : void)|null $on_expire
	 */
	public function __construct(
		private PlayerAbilityHandler $ability_handler,
		private ?Closure $on_expire = null
	){}

	public function onRun() : void{
		$this->ability_handler->removeCurrent();
	}

	public function onCancel() : void{
		if($this->on_expire !== null){
			($this->on_expire)();
		}
	}
}