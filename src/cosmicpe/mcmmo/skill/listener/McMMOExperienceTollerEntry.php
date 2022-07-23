<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use Closure;

final class McMMOExperienceTollerEntry{

	/**
	 * @param int $experience
	 * @param (Closure() : void)|null $success
	 */
	public function __construct(
		public int $experience,
		public ?Closure $success = null
	){}
}