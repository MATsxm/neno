<?php
/**
 * @package     Neno
 * @subpackage  Task
 *
 * @copyright   Copyright (c) 2014 Jensen Technologies S.L. All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class NenoTaskMonitor
 *
 * @since  1.0
 */
class NenoTaskMonitor
{
	/**
	 * @var integer
	 */
	protected static $maxExecutionTime = null;

	/**
	 * Execute tasks
	 *
	 * @param   int $maximumTask Maximum number of task
	 *
	 * @return void
	 */
	public static function runTask($maximumTask = 0)
	{
		NenoLog::log('Sending translation job to execute', 2);

		// Clean the queue
		self::cleanUp();

		if ($maximumTask == 0)
		{
			// Calculate execution time
			self::calculateMaxExecutionTime();
			$timeRemaining = self::$maxExecutionTime;

			// It means that there's no way to stop this process, let's execute just one task
			if ($timeRemaining == 0)
			{
				$task = self::fetchTask();
				self::executeTask($task);
			}
			else
			{
				// Execute tasks until we spend all the time
				while ($timeRemaining > 0)
				{
					$iniTime = time();
					$task    = self::fetchTask();
					self::executeTask($task);
					$timeRemaining -= time() - $iniTime;
				}
			}
		}
		else
		{
			for ($i = 0; $i < $maximumTask; $i++)
			{
				$task = self::fetchTask();
				self::executeTask($task);
			}
		}
	}

	/**
	 * Clean up task queue
	 *
	 * @return void
	 */
	private static function cleanUp()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->update('#__neno_tasks')
			->set('time_started = ' . $db->quote('0000-00-00 00:00:00'));

		$db->setQuery($query);
		$db->execute();

		$query
			->clear()
			->delete('#__neno_tasks')
			->where('number_of_attempts > 3');

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Calculate maximum execution time
	 *
	 * @return void
	 */
	protected static function calculateMaxExecutionTime()
	{
		if (self::$maxExecutionTime === null)
		{
			// Setting max_execution_time to 1 hour
			$result = set_time_limit(3600);

			$executionTime = 3600;

			// If no value could be set, let's get the default one.
			if ($result === false)
			{
				$executionTime = ini_get('max_execution_time');
			}

			self::$maxExecutionTime = $executionTime * 0.9;
		}
	}

	/**
	 * Load a task from the queue
	 *
	 * @return NenoTask
	 */
	protected static function fetchTask()
	{
		// Load tasks that hasn't started yet and they have less than 4 attempts
		$task = NenoTask::load(
			array (
				'attemps_filter' => array (
					'_field'     => 'number_of_attempts',
					'_condition' => '<=',
					'_value'     => 3
				),
				'time_started'   => '0000-00-00 00:00:00',
				'_order'         => array (
					'time_added' => 'ASC'
				),
				'_limit'         => 1
			)
		);

		if (empty($task))
		{
			self::addTask('job_scanner');
			self::addTask('scan', array ('group' => 1));
			self::addTask('discover');
			$task = self::fetchTask();
		}

		return $task;
	}

	/**
	 * Add a task to the database
	 *
	 * @param   string $task     Task name (type)
	 * @param   array  $taskData Task Data
	 *
	 * @return bool
	 */
	public static function addTask($task, array $taskData = array ())
	{
		$task = new NenoTask(
			array (
				'task'     => $task,
				'taskData' => json_encode($taskData)
			)
		);

		NenoLog::log('Adding translation job to execute', 2);

		return $task->persist();
	}

	/**
	 * Execute a particular task given by parameter
	 *
	 * @param   NenoTask|null $task Task to execute
	 *
	 * @return bool True on success, false otherwise
	 */
	private static function executeTask($task)
	{
		NenoLog::log('Received translation job to execute', 2);

		// If there are task to execute, let's run it
		if (!empty($task))
		{
			$task->execute();

			NenoLog::add($task->getTask() . ' task has been executed properly');
			$task->remove();

			return true;
		}

		return false;
	}
}
