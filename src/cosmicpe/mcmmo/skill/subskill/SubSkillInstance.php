<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\subskill;

final class SubSkillInstance{

	private SubSkill $sub_skill;
	private ?int $cooldown;

	public function __construct(SubSkill $sub_skill, ?int $cooldown = null){
		$this->sub_skill = $sub_skill;
		$this->cooldown = $cooldown;
	}

	public function getSubSkill() : SubSkill{
		return $this->sub_skill;
	}

	public function getCooldown() : int{
		return max($this->cooldown - time(), 0);
	}

	public function setCooldown(int $cooldown) : void{
		$this->cooldown = $cooldown > 0 ? time() + $cooldown : null;
	}
}