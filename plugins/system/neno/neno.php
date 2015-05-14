<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  System.Neno
 *
 * @copyright   Copyright (C) 2014 Jensen Technologies S.L. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 *
 */
defined('JPATH_BASE') or die;

/**
 * System plugin for Neno
 *
 * @package     Joomla.Plugin
 * @subpackage  System
 *
 * @since       1.0
 */
class PlgSystemNeno extends JPlugin
{
	/**
	 * Method to register a custom database driver
	 *
	 * @return void
	 */
	public function onAfterInitialise()
	{
		$nenoLoader = JPATH_LIBRARIES . '/neno/loader.php';

		if (file_exists($nenoLoader))
		{
			JLoader::register('NenoLoader', $nenoLoader);

			// Register the Class prefix in the autoloader
			NenoLoader::init();

			// Load custom driver.
			JFactory::$database = null;
			JFactory::$database = NenoFactory::getDbo();
		}
	}

	/**
	 * Event triggered before uninstall an extension
	 *
	 * @param   integer $extensionId Extension ID
	 *
	 * @return void
	 */
	public function onExtensionBeforeUninstall($extensionId)
	{
	}

	/**
	 * Event triggered after install an extension
	 *
	 * @param   JInstaller $installer   Installer instance
	 * @param   integer    $extensionId Extension Id
	 *
	 * @return void
	 */
	public function onExtensionAfterInstall($installer, $extensionId)
	{

	}

	/**
	 * Event triggered after update an extension
	 *
	 * @param   JInstaller $installer   Installer instance
	 * @param   integer    $extensionId Extension Id
	 *
	 * @return void
	 */
	public function onExtensionAfterUpdate($installer, $extensionId)
	{

	}

	/**
	 * This event is executed before Joomla render the page
	 *
	 * @return void
	 */
	public function onBeforeRender()
	{
		if (NenoSettings::get('schedule_task_option', 'ajax') == 'ajax' && JFactory::getApplication()->isSite())
		{
			$document = JFactory::getDocument();
			$document->addScript(JUri::root() . '/media/neno/js/ajax_module.js');
		}
	}

	public function onContentAfterSave($context, JTable $content, $isNew)
	{
		/* @var $db NenoDatabaseDriverMysqlx */
		$db        = JFactory::getDbo();
		$tableName = $content->getTableName();

		/* @var $table NenoContentElementTable */
		$table = NenoContentElementTable::load(array ('table_name' => $tableName));

		if (!empty($table))
		{
			$fields = $table->getFields();

			/* @var $field NenoContentElementField */
			foreach ($fields as $field)
			{
				if ($field->isTranslatable())
				{
					$primaryKeyData = array ();

					foreach ($content->getPrimaryKey() as $primaryKeyName => $primaryKeyValue)
					{
						$primaryKeyData[$primaryKeyName] = $primaryKeyValue;
					}

					$field->persistTranslations($primaryKeyData);
				}
			}

			$languages       = NenoHelper::getLanguages(false);
			$defaultLanguage = JFactory::getLanguage()->getDefault();

			foreach ($languages as $language)
			{
				if ($language->lang_code != $defaultLanguage)
				{
					$shadowTable = $db->generateShadowTableName($tableName, $language->lang_code);
					$properties  = $content->getProperties();
					$query       = 'REPLACE INTO ' . $db->quoteName($shadowTable) . ' ' . implode(',', $db->quoteName(array_keys($properties))) . ' VALUES(' . $db->quote($properties) . ')';
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}
}
