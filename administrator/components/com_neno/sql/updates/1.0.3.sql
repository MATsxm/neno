ALTER TABLE `#__neno_content_element_groups_x_extensions` DROP FOREIGN KEY `fk_extensions`;
ALTER TABLE `#__neno_content_element_groups_x_extensions` DROP FOREIGN KEY `fk_#__neno_content_element_groups_x_exte1`;
ALTER TABLE `#__neno_content_element_fields_x_translations` DROP FOREIGN KEY `fk_#__neno_content_element_fields_has_#__1`;
ALTER TABLE `#__neno_content_element_fields_x_translations` DROP FOREIGN KEY `fk_#__neno_content_element_fields_has_#__2`;
ALTER TABLE `#__neno_machine_translation_api_language_pairs` DROP FOREIGN KEY `translation_method_x_language_pairs_1`;
ALTER TABLE `#__neno_jobs_x_translations` DROP FOREIGN KEY `fk_job_idx1`;
ALTER TABLE `#__neno_jobs_x_translations` DROP FOREIGN KEY `fk_translation_idx1`;
ALTER TABLE `#__neno_content_element_translation_x_translation_methods` DROP FOREIGN KEY `tmi_fk`;
ALTER TABLE `#__neno_content_element_translation_x_translation_methods` DROP FOREIGN KEY `tr_fk`;
ALTER TABLE `#__neno_content_element_language_strings` DROP FOREIGN KEY `fk_#__neno_content_element_l1`;
ALTER TABLE `#__neno_content_element_fields` DROP FOREIGN KEY `fk_cef_table_idx`;
ALTER TABLE `#__neno_content_element_language_files` DROP FOREIGN KEY `fk_#__neno_content_element_1`;
ALTER TABLE `#__neno_content_element_tables` DROP FOREIGN KEY `fk_cet_group_idx`;
ALTER TABLE `#__neno_content_element_groups_x_translation_methods` DROP FOREIGN KEY `fk_preset`;
ALTER TABLE `#__neno_content_element_groups_x_translation_methods` DROP FOREIGN KEY `fk_cep_group_idx`;
ALTER TABLE `#__neno_jobs` DROP FOREIGN KEY `fk_jobs_x_tm`;
ALTER TABLE `#__neno_content_language_defaults` DROP FOREIGN KEY `fk_preset2`;