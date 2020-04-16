<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\experience;

use InvalidArgumentException;

final class SkillExperienceInstance{

	/** @var int */
	private $value;

	public function __construct(int $value){
		$this->value = $value;
	}

	public function getValue() : int{
		return $this->value;
	}

	/**
	 * @param int $value
	 * @internal use McMMOPlayer::setSkillExperience() instead.
	 */
	public function setValue(int $value) : void{
		if($value < 0){
			throw new InvalidArgumentException("Experience value must be > 0, got " . $value);
		}
		$this->value = $value;
	}

	/**
	 * @param int $value
	 * @internal use McMMOPlayer::increaseSkillExperience() instead.
	 */
	public function addValue(int $value) : void{
		if($value < 0){
			throw new InvalidArgumentException("Experience value must be > 0, got " . $value);
		}
		$this->setValue($this->value + $value);
	}

	/**
	 * @param int $value
	 * @internal use McMMOPlayer::decreaseSkillExperience() instead.
	 */
	public function subtractValue(int $value) : void{
		if($value < 0){
			throw new InvalidArgumentException("Experience value must be > 0, got " . $value);
		}
		$this->setValue($this->value - $value);
	}

	public function getRelativeValue() : int{
		return SkillExperienceManager::get()->getExperienceFromLevel($this->getLevel()) - $this->value;
	}

	public function getLevel() : int{
		return SkillExperienceManager::get()->getLevelFromExperience($this->value);
	}

	/**
	 * @param float $percentage
	 * @param int $level
	 * @internal use McMMOPlayer::setSkillLevel() instead.
	 */
	public function setLevel(int $level, float $percentage = 0.0) : void{
		if($level < 0){
			throw new InvalidArgumentException("Experience level must be > 0, got " . $level);
		}

		if($percentage < 0.0 || $percentage > 100.0){
			throw new InvalidArgumentException("Experience level percentage must be >= 0.0, <= 100.0, got " . $percentage);
		}

		$this->setValue(SkillExperienceManager::get()->getExperienceFromLevel($level));
		if($percentage > 0.0){
			$this->addValue((SkillExperienceManager::get()->getExperienceFromLevel($this->getLevel() + 1) - $this->value) * $percentage);
		}
	}

	public function __debugInfo(){
		return [
			"experience" => $this->value,
			"level" => $this->getLevel()
		];
	}
}