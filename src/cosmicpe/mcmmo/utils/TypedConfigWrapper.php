<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\utils;

use InvalidArgumentException;
use pocketmine\utils\Config;

final class TypedConfigWrapper{

	private Config $config;

	public function __construct(Config $config){
		$this->config = $config;
	}

	public function getInt(string $key) : int{
		return $this->getType($key, "int");
	}

	public function getString(string $key) : string{
		return $this->getType($key, "string");
	}

	/**
	 * @param string $key
	 * @param string $type
	 * @return mixed
	 */
	private function getType(string $key, string $type){
		$value = $this->config->get($key);
		$got_type = gettype($value);
		if($got_type !== $type){
			throw new InvalidArgumentException("Expected value of {$key} to be of type {$type}, got {$got_type}");
		}
		return $value;
	}
}