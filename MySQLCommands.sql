CREATE TABLE `static`( 
    `sess_id` VARCHAR(32) UNIQUE,
    `user_agent` VARCHAR(255),
    `language` VARCHAR(16),
    `cookies` TINYINT(1) UNSIGNED,
    `inner_width` SMALLINT(4) UNSIGNED,
    `inner_height` SMALLINT(4) UNSIGNED,
    `outer_width` SMALLINT(4) UNSIGNED,
    `outer_height` SMALLINT(4) UNSIGNED,
    `downlink` DECIMAL(7,3) UNSIGNED,
    `effective_type` VARCHAR(8),
    `rtt` SMALLINT(4) UNSIGNED,
    `save_data` TINYINT(1) UNSIGNED,
    PRIMARY KEY (`sess_id`)
);

CREATE TABLE `performance`(
	`sess_id` VARCHAR(32) UNIQUE,
	`start_time`  TIMESTAMP(6) NULL,
	`fetch_start`  TIMESTAMP(6) NULL,
	`request_start`  TIMESTAMP(6) NULL,
	`response_start`  TIMESTAMP(6) NULL,
	`response_end`  TIMESTAMP(6) NULL,
	`dom_interactive`  TIMESTAMP(6) NULL,
	`dom_loaded_start`  TIMESTAMP(6) NULL,
	`dom_loaded_end`  TIMESTAMP(6) NULL,
	`dom_complete`  TIMESTAMP(6) NULL,
	`load_event_start`  TIMESTAMP(6) NULL,
	`load_event_end`  TIMESTAMP(6) NULL,
	`duration` DECIMAL(10, 3) UNSIGNED,
	`transfer_size` MEDIUMINT(6) UNSIGNED,
	`decoded_body_size` MEDIUMINT(6) UNSIGNED,
	PRIMARY KEY (`sess_id`)
);

CREATE TABLE `activity`(
	`id` INT(10) SERIAL DEFAULT VALUE,
	`sess_id` VARCHAR(32),
	`activity_type` ENUM('mouse_position','mouse_clicks','key_down','key_up','timing'),
	`activity_info` JSON,
	`alt_key` TINYINT(1) NULL,
	`ctrl_key` TINYINT(1) NULL,
	`shift_key` TINYINT(1) NULL,
	`timestamp` DECIMAL(10, 3) UNSIGNED NULL,
	PRIMARY KEY (`id`)
);

INSERT INTO `static` VALUES ('test','ssh browser','english',1,42,42,42,42,42.690,'5g',42,1);
INSERT INTO `performance` VALUES ('test',0.0,42.690,42.690,42.690,42.690,42.690,42.690,42.690,42.690,42.690,42.690,42.690,42,69);
INSERT INTO `activity`(sess_id, activity_type, activity_info, alt_key, ctrl_key, shift_key, timestamp)  VALUES (0,'test','timing','{"pageEnter":0,"pageLeave":42,"currPage":"test.com"}',,,,);