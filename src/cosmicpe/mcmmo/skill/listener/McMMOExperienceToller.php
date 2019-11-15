<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use Closure;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\Skill;
use cosmicpe\mcmmo\skill\SkillManager;

final class McMMOExperienceToller{

	/** @var int|Closure[] */
	private $experiences = [];

	public function add(Skill $skill, int $experience, ?Closure $success = null) : void{
		if($experience > 0){
			$this->experiences[$skill->getIdentifier()][] = [$experience, $success];
		}
	}

	public function apply(McMMOPlayer $player) : void{
		foreach($this->experiences as $skill => $additions){
			foreach($additions as [$experience, $callback]){
				if($player->increaseSkillExperience(SkillManager::get($skill), $experience) && $callback !== null){
					$callback();
				}
				var_dump($player->getSkill(SkillManager::get($skill)));
			}
		}
	}
}