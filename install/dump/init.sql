CREATE SEQUENCE "role_actions_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "roles_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "role2action_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "users_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE SEQUENCE "cron_jobs_id_seq" INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "references"
(
    "reference" uuid NOT NULL,
    CONSTRAINT "references_reference" PRIMARY KEY ("reference")
) WITH (oids = false);

CREATE TABLE "role_actions"
(
    "id"        integer DEFAULT nextval('role_actions_id_seq') NOT NULL,
    "name"      character varying(32)                          NOT NULL,
    "is_system" boolean DEFAULT false                          NOT NULL,
    "is_active" boolean DEFAULT true                           NOT NULL,
    "cdate"     integer                                        NOT NULL,
    "mdate"     integer,
    "ddate"     integer,
    CONSTRAINT "role_actions_id" PRIMARY KEY ("id"),
    CONSTRAINT "role_actions_name" UNIQUE ("name")
) WITH (oids = false);

CREATE TABLE "roles"
(
    "id"        integer DEFAULT nextval('roles_id_seq') NOT NULL,
    "name"      character varying(32)                   NOT NULL,
    "parent_id" integer,
    "is_system" boolean DEFAULT false                   NOT NULL,
    "is_active" boolean DEFAULT true                    NOT NULL,
    "cdate"     integer                                 NOT NULL,
    "mdate"     integer,
    "ddate"     integer,
    CONSTRAINT "roles_id" PRIMARY KEY ("id"),
    CONSTRAINT "roles_name" UNIQUE ("name"),
    CONSTRAINT "roles_parent_id_fkey" FOREIGN KEY ("parent_id") REFERENCES roles ("id")
        ON UPDATE CASCADE
        ON DELETE SET NULL
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "role2action"
(
    "id"         integer DEFAULT nextval('role2action_id_seq') NOT NULL,
    "role_id"    integer                                       NOT NULL,
    "action_id"  integer                                       NOT NULL,
    "is_allowed" boolean DEFAULT true                          NOT NULL,
    CONSTRAINT "role2action_id" PRIMARY KEY ("id"),
    CONSTRAINT "role2action_role_id_action_id" UNIQUE ("role_id", "action_id"),
    CONSTRAINT "role2action_action_id_fkey" FOREIGN KEY ("action_id") REFERENCES "role_actions" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE,
    CONSTRAINT "role2action_role_id_fkey" FOREIGN KEY ("role_id") REFERENCES "roles" ("id")
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "users"
(
    "id"              integer DEFAULT nextval('users_id_seq') NOT NULL,
    "login"           character varying(64)                   NOT NULL,
    "email"           character varying(128)                  NOT NULL,
    "password_hash"   character varying(128),
    "api_token"       character varying(128),
    "web_token"       character varying(128),
    "role_id"         integer                                 NOT NULL,
    "last_login_date" integer,
    "is_active"       boolean DEFAULT true                    NOT NULL,
    "cdate"           integer                                 NOT NULL,
    "mdate"           integer,
    "ddate"           integer,
    CONSTRAINT "users_id" PRIMARY KEY ("id"),
    CONSTRAINT "users_login" UNIQUE ("login"),
    CONSTRAINT "users_email" UNIQUE ("email"),
    CONSTRAINT "users_role_id_fkey" FOREIGN KEY ("role_id") REFERENCES "roles" ("id")
        ON UPDATE CASCADE
        ON DELETE SET NULL
        NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "cron_jobs"
(
    "id"                integer               DEFAULT nextval('cron_jobs_id_seq') NOT NULL,
    "alias"             character varying(128)                                    NOT NULL,
    "controller"        character varying(128)                                    NOT NULL,
    "controller_method" character varying(128)                                    NOT NULL,
    "interval"          integer                                                   NOT NULL,
    "time_next_exec"    integer,
    "status"            character varying(16) DEFAULT 'scheduled'                 NOT NULL,
    "error_message"     character varying(256),
    "is_active"         boolean               DEFAULT true                        NOT NULL,
    "cdate"             integer                                                   NOT NULL,
    "mdate"             integer,
    "ddate"             integer,
    CONSTRAINT "cron_jobs_id" PRIMARY KEY ("id"),
    CONSTRAINT "cron_jobs_alias" UNIQUE ("alias"),
    CONSTRAINT "cron_jobs_controller_controller_method_interval" UNIQUE ("controller", "controller_method", "interval")
) WITH (oids = false);

CREATE INDEX "role_actions_is_active" ON "role_actions" USING btree ("is_active");
CREATE INDEX "role_actions_cdate" ON "role_actions" USING btree ("cdate");
CREATE INDEX "role_actions_mdate" ON "role_actions" USING btree ("mdate");
CREATE INDEX "role_actions_ddate" ON "role_actions" USING btree ("ddate");
CREATE INDEX "role_actions_id_name" ON "role_actions" USING btree ("id", "name");
CREATE INDEX "role_actions_is_active_ddate" ON "role_actions" USING btree ("is_active", "ddate");
CREATE INDEX "role_actions_id_is_active" ON "role_actions" USING btree ("id", "is_active");
CREATE INDEX "role_actions_id_is_active_ddate" ON "role_actions" USING btree ("id", "is_active", "ddate");
CREATE INDEX "role_actions_name_is_active" ON "role_actions" USING btree ("name", "is_active");
CREATE INDEX "role_actions_name_is_active_ddate" ON "role_actions" USING btree ("name", "is_active", "ddate");

CREATE INDEX "roles_parent_id" ON "roles" USING btree ("parent_id");
CREATE INDEX "roles_is_active" ON "roles" USING btree ("is_active");
CREATE INDEX "roles_cdate" ON "roles" USING btree ("cdate");
CREATE INDEX "roles_mdate" ON "roles" USING btree ("mdate");
CREATE INDEX "roles_ddate" ON "roles" USING btree ("ddate");
CREATE INDEX "roles_id_name" ON "roles" USING btree ("id", "name");
CREATE INDEX "roles_id_is_active" ON "roles" USING btree ("id", "is_active");
CREATE INDEX "roles_id_is_active_ddate" ON "roles" USING btree ("id", "is_active", "ddate");
CREATE INDEX "roles_name_is_active" ON "roles" USING btree ("name", "is_active");
CREATE INDEX "roles_name_is_active_ddate" ON "roles" USING btree ("name", "is_active", "ddate");

CREATE INDEX "role2action_action_id" ON "role2action" USING btree ("action_id");
CREATE INDEX "role2action_role_id" ON "role2action" USING btree ("role_id");
CREATE INDEX "role2action_is_allowed" ON "role2action" USING btree ("is_allowed");
CREATE INDEX "role2action_role_id_action_id_is_allowed" ON "role2action"
    USING btree ("role_id", "action_id", "is_allowed");

CREATE INDEX "users_api_token" ON "users" USING btree ("api_token");
CREATE INDEX "users_is_active" ON "users" USING btree ("is_active");
CREATE INDEX "users_cdate" ON "users" USING btree ("cdate");
CREATE INDEX "users_mdate" ON "users" USING btree ("mdate");
CREATE INDEX "users_ddate" ON "users" USING btree ("ddate");
CREATE INDEX "users_password_hash" ON "users" USING btree ("password_hash");
CREATE INDEX "users_role_id" ON "users" USING btree ("role_id");
CREATE INDEX "users_web_token" ON "users" USING btree ("web_token");
CREATE INDEX "users_id_login" ON "users" USING btree ("id", "login");
CREATE INDEX "users_id_api_token" ON "users" USING btree ("id", "api_token");
CREATE INDEX "users_id_api_token_is_active" ON "users" USING btree ("id", "api_token", "is_active");
CREATE INDEX "users_id_api_token_is_active_ddate" ON "users" USING btree ("id", "api_token", "is_active", "ddate");
CREATE INDEX "users_id_is_active" ON "users" USING btree ("id", "is_active");
CREATE INDEX "users_id_is_active_ddate" ON "users" USING btree ("id", "is_active", "ddate");
CREATE INDEX "users_id_web_token" ON "users" USING btree ("id", "web_token");
CREATE INDEX "users_id_web_token_is_active" ON "users" USING btree ("id", "web_token", "is_active");
CREATE INDEX "users_id_web_token_is_active_ddate" ON "users" USING btree ("id", "web_token", "is_active", "ddate");
CREATE INDEX "users_login_password_hash" ON "users" USING btree ("login", "password_hash");
CREATE INDEX "users_login_password_hash_is_active" ON "users" USING btree ("login", "password_hash", "is_active");
CREATE INDEX "users_login_password_hash_is_active_ddate" ON "users"
    USING btree ("login", "password_hash", "is_active", "ddate");

CREATE INDEX "cron_jobs_controller" ON "cron_jobs" USING btree ("controller");
CREATE INDEX "cron_jobs_controller_method" ON "cron_jobs" USING btree ("controller_method");
CREATE INDEX "cron_jobs_interval" ON "cron_jobs" USING btree ("interval");
CREATE INDEX "cron_jobs_time_next_exec" ON "cron_jobs" USING btree ("time_next_exec");
CREATE INDEX "cron_jobs_time_status" ON "cron_jobs" USING btree ("status");
CREATE INDEX "cron_jobs_is_active" ON "cron_jobs" USING btree ("is_active");
CREATE INDEX "cron_jobs_cdate" ON "cron_jobs" USING btree ("cdate");
CREATE INDEX "cron_jobs_mdate" ON "cron_jobs" USING btree ("mdate");
CREATE INDEX "cron_jobs_ddate" ON "cron_jobs" USING btree ("ddate");
CREATE INDEX "cron_jobs_id_alias" ON "cron_jobs" USING btree ("id", "alias");
CREATE INDEX "cron_jobs_id_controller_controller_method_interval" ON "cron_jobs"
    USING btree ("id", "controller", "controller_method", "interval");
CREATE INDEX "cron_jobs_is_active_ddate" ON "cron_jobs" USING btree ("is_active", "ddate");
CREATE INDEX "cron_jobs_time_next_exec_is_active_ddate" ON "cron_jobs"
    USING btree ("time_next_exec", "is_active", "ddate");

INSERT INTO "cron_jobs" ("id", "alias", "controller", "controller_method", "interval", "cdate")
VALUES (1, 'Router', 'cron', 'router', 28800/* 8 hours */, CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER)),
       (2, 'Translations', 'cron', 'translations', 28800/* 8 hours */,
        CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS INTEGER));

ALTER SEQUENCE "cron_jobs_id_seq" RESTART WITH 3;
