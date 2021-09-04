DROP TABLE IF EXISTS "cron_jobs";

DROP SEQUENCE IF EXISTS cron_jobs_id_seq;

CREATE SEQUENCE cron_jobs_id_seq;

CREATE TABLE "cron_jobs"
(
    "id"               integer        DEFAULT nextval('cron_jobs_id_seq') NOT NULL,
    "action"           character varying(128)                             NOT NULL,
    "interval"         numeric(8, 0),
    "time_next_exec"   numeric(11, 0) DEFAULT '-1'                        NOT NULL,
    "last_exec_status" boolean        DEFAULT true                        NOT NULL,
    "is_active"        boolean        DEFAULT false                       NOT NULL,
    "error_message"    character varying(255),
    CONSTRAINT "cron_id" PRIMARY KEY ("id"),
    CONSTRAINT "cron_jobs_action_interval" UNIQUE ("action", "interval")
);

CREATE INDEX "cron_action" ON "cron_jobs" USING btree ("action");

CREATE INDEX "cron_action_time_next_exec"
    ON "cron_jobs" USING btree ("action", "time_next_exec");

CREATE INDEX "cron_action_time_next_exec_is_active"
    ON "cron_jobs" USING btree ("action", "time_next_exec", "is_active");

CREATE INDEX "cron_is_active"
    ON "cron_jobs" USING btree ("is_active");

CREATE INDEX "cron_last_next_status"
    ON "cron_jobs" USING btree ("last_exec_status");

CREATE INDEX "cron_time_next_exec"
    ON "cron_jobs" USING btree ("time_next_exec");

INSERT INTO "cron_jobs" ("action",
                         "interval",
                         "time_next_exec",
                         "last_exec_status",
                         "is_active",
                         "error_message")
VALUES ('router',
        '7200',
        '-1',
        '1',
        '1',
        NULL);

INSERT INTO "cron_jobs" ("action",
                         "interval",
                         "time_next_exec",
                         "last_exec_status",
                         "is_active",
                         "error_message")
VALUES ('translations',
        '36000',
        '-1',
        '1',
        '1',
        NULL);
