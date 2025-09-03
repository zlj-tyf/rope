-- SQL script to remove field column from matches table
-- This removes all field-related functionality from the database

USE rope;

-- Remove the field column from matches table
ALTER TABLE `matches` DROP COLUMN `field`;

-- Verify the table structure after migration
DESCRIBE `matches`;