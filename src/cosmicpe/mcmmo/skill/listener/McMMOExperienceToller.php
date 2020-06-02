<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use Closure;
use cosmicpe\mcmmo\event\player\skill\McMMOPlayerSkillExperienceChangeEvent;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\Skill;
use cosmicpe\mcmmo\skill\SkillManager;

final class McMMOExperienceToller{

	/** @var McMMOExperienceTollerEntry[][] */
	private $experiences = [];

	public function add(Skill $skill, int $experience, ?Closure $success = null) : void{
		if($experience > 0){
			$this->experiences[$skill->getIdentifier()][] = new McMMOExperienceTollerEntry($experience, $success);
		}
	}

	public function apply(McMMOPlayer $player) : void{
		foreach($this->experiences as $skill => $additions){
			foreach($additions as $entry){
				if($player->increaseSkillExperience(SkillManager::get($skill), $entry->experience, McMMOPlayerSkillExperienceChangeEvent::CAUSE_SKILL) && $entry->success !== null){
					($entry->success)();
				}
			}
		}
	}
}