-- Add module and lesson tracking to daily tasks
ALTER TABLE `daily_tasks` 
ADD COLUMN `module_number` INT NULL AFTER `day_number`,
ADD COLUMN `lesson_number` INT NULL AFTER `module_number`;
