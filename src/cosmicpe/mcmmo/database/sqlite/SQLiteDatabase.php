<?php

declare(strict_types=1);

namespace cosmicpe\mcmmo\database\sqlite;

use Closure;
use cosmicpe\mcmmo\database\IDatabase;
use cosmicpe\mcmmo\McMMO;
use cosmicpe\mcmmo\player\McMMOPlayer;
use cosmicpe\mcmmo\skill\SkillInstance;
use cosmicpe\mcmmo\skill\SkillManager;
use cosmicpe\mcmmo\skill\subskill\SubSkillInstance;
use cosmicpe\mcmmo\skill\subskill\SubSkillManager;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;

class SQLiteDatabase implements IDatabase{

	private DataConnector $database;

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

	public function load(UuidInterface $uuid, Closure $callback) : void{
		$db = $this->database;
		$db->executeSelect(SQLiteStmt::LOAD_SKILLS, ["uuid" => $uuid->toString()], static function(array $rows) use ($callback, $uuid, $db) : void{
			$skills = [];
			foreach($rows as [
				"skill" => $skill,
				"cooldown" => $cooldown,
				"experience" => $experience
			]){
				$skills[$skill] = new SkillInstance(SkillManager::get($skill), $cooldown, $experience);
			}
			$db->executeSelect(SQLiteStmt::LOAD_SUB_SKILLS, ["uuid" => $uuid->toString()], static function(array $rows) use($callback, $uuid, $skills) : void{
				$sub_skills = [];
				foreach($rows as [
					"sub_skill" => $sub_skill,
					"cooldown" => $cooldown
				]){
					$sub_skills[$sub_skill] = new SubSkillInstance(SubSkillManager::get($sub_skill), $cooldown);
				}
				$callback(new McMMOPlayer($uuid, $skills, $sub_skills));
			});
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

		$sub_skill_id = null;
		$sub_skill_cooldown = null;
		$entry = ["uuid" => $uuid, "sub_skill" => &$sub_skill_id, "cooldown" => &$sub_skill_cooldown];
		foreach($player->getSubSkills() as $sub_skill_id => $sub_skill){
			$sub_skill_cooldown = $time + $sub_skill->getCooldown();
			$this->database->executeInsert(SQLiteStmt::SAVE_SUB_SKILLS, $entry);
		}
	}

	public function close() : void{
		$this->database->waitAll();
		$this->database->close();
	}
}