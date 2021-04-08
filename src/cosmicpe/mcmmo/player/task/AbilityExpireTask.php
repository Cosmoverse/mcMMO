<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\player\task;

use Closure;
use cosmicpe\mcmmo\player\PlayerAbilityHandler;
use pocketmine\scheduler\Task;

class AbilityExpireTask extends Task{

	private PlayerAbilityHandler $ability_handler;
	private ?Closure $on_expire;

	public function __construct(PlayerAbilityHandler $ability_handler, ?Closure $on_expire = null){
		$this->ability_handler = $ability_handler;
		$this->on_expire = $on_expire;
	}

	public function onRun() : void{
		$this->ability_handler->removeCurrent();
	}

	public function onCancel() : void{
		if($this->on_expire !== null){
			($this->on_expire)();
		}
	}
}