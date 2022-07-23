<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\skill\listener;

use Closure;

final class McMMOExperienceTollerEntry{

	public function __construct(
		public int $experience,
		public ?Closure $success = null
	){}
}