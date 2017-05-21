-- Empty for proper setup of the J!2.5 schemas table entry.

UPDATE `#__nucleonplus_accounts` AS a
INNER JOIN `#__users` AS u ON a.`user_id` = u.`id`
SET a.`user_name` = u.`name`;