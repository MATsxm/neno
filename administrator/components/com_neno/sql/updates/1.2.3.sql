ALTER TABLE `#__neno_content_element_table_filters` DROP INDEX table_id;
`
ALTER TABLE `#__neno_content_element_language_files` ADD `translate` TINYINT(1) NOT NULL DEFAULT '1';