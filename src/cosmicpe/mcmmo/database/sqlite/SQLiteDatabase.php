<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\database\sqlite;

use Closure;
use cosmicpe\mcmmo\database\IDatabase;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\SkillInstance;
use cosmicpe\mcmmo\skill\SkillManager;
use pocketmine\utils\UUID;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use ReflectionClass;

class SQLiteDatabase implements IDatabase{

	/** @var DataConnector */
	private $database;

	public function __construct(McMMO $plugin){
		$this->database = libasynql::create($plugin, $plugin->getConfig()->get("database"), [
			"sqlite" => "sqlite.sql"
		]);
		$this->database->setLoggingQueries(true);

		foreach((new ReflectionClass(SQLiteStmt::class))->getConstants() as $constant => $value){
			if(strpos($constant, "INIT_") === 0){
				$this->database->executeGeneric($value);
			}
		}
	}

	public function load(UUID $uuid, Closure $callback) : void{
		$this->database->executeSelect(SQLiteStmt::LOAD_SKILLS, ["uuid" => $uuid->toString()], static function(array $rows) use ($callback, $uuid) : void{
			$skills = [];
			foreach($rows as [
				"skill" => $skill,
				"cooldown" => $cooldown,
				"experience" => $experience
			]){
				$skills[$skill] = new SkillInstance(SkillManager::get($skill), $cooldown, $experience);
			}
			$callback(new McMMOPlayer($uuid, $skills));
		});
	}

	public function save(McMMOPlayer $player) : void{
		$uuid = $player->getUniqueId()->toString();
		$time = time();

		$this->database->executeInsert(SQLiteStmt::SAVE_PLAYER, ["uuid" => $uuid, "last_update" => $time]);

		$skill_id = null;
		$skill_cooldown = null;
		$skill_experience = null;
		$entry = ["uuid" => $uuid, "skill" => &$skill_id, "cooldown" => &$skill_cooldown, "experience" => &$skill_experience];
		foreach($player->getSkills() as $skill_id => $skill){
			$skill_cooldown = $time + $skill->getCooldown();
			$skill_experience = $skill->getExperience()->getValue();
			$this->database->executeInsert(SQLiteStmt::SAVE_SKILLS, $entry);
		}
	}

	public function close() : void{
		$this->database->close();
	}
}