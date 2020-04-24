<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use Closure;

final class McMMOExperienceTollerEntry{

	/** @var int */
	public $experience;

	/** @var Closure|null */
	public $success;

	public function __construct(int $experience, ?Closure $success = null){
		$this->experience = $experience;
		$this->success = $success;
	}
}