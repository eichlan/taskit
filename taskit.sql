SET client_min_messages TO 'WARNING';

CREATE TABLE "user" (
	userid SERIAL PRIMARY KEY NOT NULL,
	name VARCHAR(80) NOT NULL,
	realname VARCHAR(80),
	email VARCHAR(1024) NOT NULL,
	password CHAR(60) NOT NULL,
	timezone VARCHAR(64) NOT NULL,
	settings TEXT,
	created TIMESTAMP NOT NULL DEFAULT NOW(),
	lastseen TIMESTAMP NOT NULL DEFAULT NOW()
);
CREATE UNIQUE INDEX user_name ON "user"(LOWER(name));

CREATE TABLE user_view (
	viewid SERIAL PRIMARY KEY NOT NULL,
	userid BIGINT REFERENCES "user"(userid) ON DELETE CASCADE,
	section BIGINT,
	sequence BIGINT,
	name VARCHAR(64),
	definition TEXT
);

CREATE TABLE default_view (
	viewid SERIAL PRIMARY KEY NOT NULL,
	section BIGINT,
	sequence BIGINT,
	name VARCHAR(64),
	definition TEXT
);

CREATE TABLE sessions (
	id CHAR(32) PRIMARY KEY,
	expire BIGINT,
	data TEXT
);
CREATE INDEX sessions_expire ON sessions(expire);

--
-- This comes (almost) straight from Yii, slight modifications to make it
-- integrate better with this database have been done
--
CREATE TABLE auth_item (
	name VARCHAR(64) NOT NULL PRIMARY KEY,
	type BIGINT NOT NULL,
	description TEXT,
	bizrule TEXT,
	data TEXT
);

CREATE TABLE auth_item_child (
	parent VARCHAR(64) REFERENCES auth_item(name) ON DELETE CASCADE
		ON UPDATE CASCADE NOT NULL,
	child VARCHAR(64) REFERENCES auth_item(name) ON DELETE CASCADE
		ON UPDATE CASCADE NOT NULL,
	PRIMARY KEY(parent, child)
);

CREATE TABLE auth_assignment (
	itemname VARCHAR(64) REFERENCES auth_item ON DELETE CASCADE
		ON UPDATE CASCADE NOT NULL,
	userid BIGINT, -- The way Yii sets this it cannot be a foreign key
	bizrule TEXT,
	data TEXT,
	PRIMARY KEY(itemname, userid)
);
--
-- End of yii role tables
--

CREATE TABLE team (
	teamid SERIAL NOT NULL PRIMARY KEY,
	name VARCHAR(80),
	description TEXT,
	ownerid BIGINT REFERENCES "user"(userid)
);

CREATE TABLE team_priority (
	teamid BIGINT REFERENCES team(teamid) ON DELETE CASCADE,
	priority BIGINT NOT NULL,
	name VARCHAR(64) NOT NULL,
	PRIMARY KEY(teamid, priority)
);

CREATE TABLE team_task_type (
	typeid SERIAL NOT NULL PRIMARY KEY,
	teamid BIGINT REFERENCES team(teamid) ON DELETE CASCADE,
	name VARCHAR(64) NOT NULL
);

CREATE TABLE team_task_status (
	statusid SERIAL NOT NULL PRIMARY KEY,
	teamid BIGINT REFERENCES team(teamid) ON DELETE CASCADE,
	name VARCHAR(64) NOT NULL,
	open BOOLEAN DEFAULT true NOT NULL
);

CREATE TABLE team_user (
	teamid BIGINT REFERENCES team(teamid)
		ON DELETE CASCADE,
	userid BIGINT REFERENCES "user"(userid) ON DELETE CASCADE,
	flags BIGINT DEFAULT 0,
	PRIMARY KEY(teamid, userid)
);

CREATE TABLE project (
	projectid SERIAL NOT NULL PRIMARY KEY,
	teamid BIGINT REFERENCES team(teamid)
		ON DELETE CASCADE,
	parentid BIGINT REFERENCES project(projectid) ON DELETE CASCADE,
	open BOOLEAN DEFAULT TRUE,
	name VARCHAR(80),
	description TEXT,
	created TIMESTAMP DEFAULT NOW() NOT NULL,
	updated TIMESTAMP DEFAULT NOW() NOT NULL
);

CREATE TABLE milestone (
	milestoneid SERIAL NOT NULL PRIMARY KEY,
	projectid BIGINT REFERENCES project(projectid) ON DELETE CASCADE,
	name VARCHAR(80),
	description TEXT,
	created TIMESTAMP DEFAULT NOW() NOT NULL,
	goal TIMESTAMP DEFAULT NULL
);

CREATE TABLE task (
	taskid SERIAL NOT NULL PRIMARY KEY,
	authorid BIGINT REFERENCES "user"(userid),
	projectid BIGINT REFERENCES project(projectid) ON DELETE CASCADE,
	teamid BIGINT REFERENCES team(teamid) ON DELETE CASCADE,
	milestoneid BIGINT REFERENCES milestone(milestoneid) DEFAULT NULL,
	priority BIGINT DEFAULT 0,
	statusid BIGINT REFERENCES team_task_status,
	typeid BIGINT REFERENCES team_task_type,
	complexity BIGINT,
	name VARCHAR(80),
	description TEXT,
	created TIMESTAMP DEFAULT NOW() NOT NULL,
	updated TIMESTAMP DEFAULT NOW() NOT NULL,

	FOREIGN KEY (teamid, priority) REFERENCES team_priority
);

CREATE TABLE task_assignment (
	taskid BIGINT REFERENCES task(taskid) ON DELETE CASCADE,
	userid BIGINT REFERENCES "user"(userid) ON DELETE CASCADE,
	PRIMARY KEY(taskid, userid)
);

CREATE TABLE task_comment (
	commentid SERIAL NOT NULL PRIMARY KEY,
	authorid BIGINT REFERENCES "user"(userid),
	taskid BIGINT REFERENCES task(taskid) ON DELETE CASCADE,
	comment TEXT,
	type BIGINT DEFAULT 1,
	created TIMESTAMP DEFAULT NOW() NOT NULL
);

CREATE TABLE file_attachment (
	fileid UUID NOT NULL PRIMARY KEY,
	authorid BIGINT REFERENCES "user"(userid),
	projectid BIGINT REFERENCES project(projectid),
	taskid BIGINT REFERENCES task(taskid),
	commentid BIGINT REFERENCES task_comment(commentid),
	filename VARCHAR(1024),
	mimetype VARCHAR(1024),
	sha1 CHAR(40),
	size BIGINT,
	created TIMESTAMP DEFAULT NOW() NOT NULL
);

CREATE TABLE sub_task (
	subtaskid SERIAL NOT NULL PRIMARY KEY,
	taskid BIGINT REFERENCES task(taskid) ON DELETE CASCADE,
	name VARCHAR(80),
	description TEXT,
	created TIMESTAMP DEFAULT NOW() NOT NULL
);

CREATE TABLE code_unit (
	unitid SERIAL NOT NULL PRIMARY KEY,
	projectid BIGINT REFERENCES project(projectid),
	name VARCHAR(128),
	type VARCHAR(32),
	file VARCHAR(128),
	parent VARCHAR(128),
	language VARCHAR(16),
	ignore BOOLEAN DEFAULT false,
	complete REAL
);

CREATE TABLE code_unit_task (
	unitid BIGINT REFERENCES code_unit(unitid) ON DELETE CASCADE,
	taskid BIGINT REFERENCES task(taskid) ON DELETE CASCADE,
	PRIMARY KEY(unitid, taskid)
);

CREATE TABLE global_property (
	name VARCHAR(64) NOT NULL PRIMARY KEY,
	value TEXT
);

CREATE TABLE invitation_code (
	code CHAR(16) NOT NULL PRIMARY KEY,
	enabled BOOLEAN DEFAULT true,
	senderid BIGINT REFERENCES "user"(userid),
	recipientid BIGINT REFERENCES "user"(userid),
	created TIMESTAMP DEFAULT now() NOT NULL
);

CREATE FUNCTION resequence_user_views( ids BIGINT[] ) RETURNS void AS $fnc$
DECLARE
	i BIGINT;
BEGIN
	FOR i IN 1..array_length(ids, 1) LOOP
		UPDATE user_view SET sequence=i WHERE viewid=ids[i];
	END LOOP;
END;
$fnc$ LANGUAGE plpgsql;

CREATE FUNCTION reset_user_views( usrid BIGINT ) RETURNS void AS $proc$
DECLARE
	v default_view%ROWTYPE;
BEGIN
	DELETE FROM user_view WHERE userid=usrid;
	FOR v IN SELECT * FROM default_view LOOP
		INSERT INTO user_view (userid, section, sequence, name, definition)
			VALUES (usrid, v.section, v.sequence, v.name, v.definition);
	END LOOP;
END;
$proc$ LANGUAGE plpgsql;

CREATE FUNCTION reset_all_user_views() RETURNS void AS $proc$
DECLARE
	usr "user"%ROWTYPE;
BEGIN
	FOR usr IN SELECT userid FROM "user" LOOP
		PERFORM reset_user_views( usr.userid );
	END LOOP;
END;
$proc$ LANGUAGE plpgsql;

\i taskit_seed.sql
