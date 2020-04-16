# mcMMO

## API

### Player API
To fetch an `McMMOPlayer` instance of an online player
```php
/** @var Player $player */
$mcmmo_player = mcMMO::getInstance()->getPlayerManager()->get($player);
```
As the player data is fetched asynchronously, `$mcmmo_player` will be null at the time the player data is being fetched from the database, so you must do a null check.

To increase the player's acrobatics skill experience
```php
$mcmmo_player->increaseSkillExperience(SkillManager::get(SkillIds::ACROBATICS), 125);
```

To increase the player's acrobatics skill level by 3
```php
$mcmmo_player->increaseSkillLevel(SkillManager::get(SkillIds::ACROBATICS), 3);
```

Note that `increaseSkillExperience` and `increaseSkillLevel` return a boolean indicating whether `McMMOPlayerSkillExperienceChangeEvent` was not cancelled (returns true if the event wasn't cancelled).


### Events API
#### McMMOPlayerAbilityActivateEvent
Called when player activates an ability (f.e: giga drill breaker).
```php
/**
 * Returns the skill ability that is being activated.
 * @return Ability
 */
McMMOPlayerAbilityActivateEvent::getAbility() : Ability;

/**
 * How long the ability lasts (in seconds).
 * @return int
 */
McMMOPlayerAbilityActivateEvent::getDuration() : int;

/**
 * Change how long the ability lasts
 * @param int $duration (in seconds)
 */
McMMOPlayerAbilityActivateEvent::setDuration(int $duration) : void;
```
#### McMMOPlayerSkillExperienceChangeEvent
Called when a player gains experience in a skill.
```php
/**
 * Returns the old experience value.
 * @return int
 */
McMMOPlayerSkillExperienceChangeEvent::getOldExperience() : int;

/**
 * Returns the new experience value.
 * @return int
 */
McMMOPlayerSkillExperienceChangeEvent::getNewExperience() : int;

/**
 * Returns the old experience level.
 * @return int
 */
McMMOPlayerSkillExperienceChangeEvent::getOldLevel() : int;

/**
 * Returns the new experience level.
 * @return int
 */
McMMOPlayerSkillExperienceChangeEvent::getNewLevel() : int;

/**
 * Sets the new experience value.
 * @param int $value
 */
McMMOPlayerSkillExperienceChangeEvent::setNewExperience(int $value) : void;
```
