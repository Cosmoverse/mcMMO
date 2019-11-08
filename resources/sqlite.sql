-- #! sqlite
-- #{ cosmicpe.mcmmo
-- #    { init
-- #        { players
CREATE TABLE IF NOT EXISTS players(
    uuid CHAR(36) NOT NULL PRIMARY KEY,
    last_update INT UNSIGNED NOT NULL
);
-- #        }
-- #        { skills
CREATE TABLE IF NOT EXISTS skills(
    uuid CHAR(36) NOT NULL,
    skill VARCHAR(32) NOT NULL,
    cooldown INT UNSIGNED NOT NULL,
    experience INT UNSIGNED NOT NULL,
    PRIMARY KEY(uuid, skill),
    FOREIGN KEY(uuid)
        REFERENCES players(uuid)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
-- #        }
-- #    }
-- #    { skills
-- #        { load
-- #            :uuid string
SELECT skill, cooldown, experience FROM skills WHERE uuid=:uuid;
-- #        }
-- #        { save
-- #            :uuid string
-- #            :skill string
-- #            :cooldown int
-- #            :experience int
INSERT OR REPLACE INTO skills(uuid, skill, cooldown, experience)
VALUES(:uuid, :skill, :cooldown, :experience);
-- #        }
-- #    }
-- #    { players
-- #        { save
-- #            :uuid string
-- #            :last_update int
INSERT OR REPLACE INTO players(uuid, last_update)
VALUES(:uuid, :last_update);
-- #        }
-- #    }
-- #}