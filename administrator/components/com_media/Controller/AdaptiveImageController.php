<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\Media\Administrator\Interfaces\AdaptiveImageInterface;

\JLoader::import('joomla.filesystem.file');

/**
 * Adaptive Image Controller Class
 *
 * Used to set the focus point and save it into filesystem
 *
 * Used to get the focus point while rendering the page on the frontend
 *
 * @since  4.0.0
 */
class AdaptiveImageController extends BaseController  implements AdaptiveImageInterface
{
	/**
	 * Location for file storing the data focus point.
	 *
	 * @var string
	 *
	 * @since 4.0.0
	 */
	protected static $dataLocation = JPATH_PLUGINS . '/media-action/smartcrop/focus.json';
	/**
	 * Execute a task by triggering a method in the derived class.
	 *
	 * @param   string  $task  The task to perform.
	 *
	 * @return  mixed   The value returned by the called method.
	 *
	 * @since   4.0.0
	 */
	public function execute($task)
	{
		$this->app->setHeader('Content-Type', 'application/json');

		$this->checkStorage(static::$dataLocation);

		$filePath = $this->imageSrc();

		if ($task == "setfocus")
		{
			$dataFocus = array (
				"data-focus-top" => $_GET['data-focus-top'],
				"data-focus-left" => $_GET['data-focus-left'],
				"data-focus-bottom" => $_GET['data-focus-bottom'],
				"data-focus-right" => $_GET['data-focus-right']
			);

			$this->setFocus($dataFocus, $filePath);
		}

		$this->app->close();

	}
	/**
	 * Function to set the focus point
	 *
	 * index.php?option=com_media&task=adaptiveimage.setfocus&path=/images/sampledata/fruitshop/bananas_1.jpg
	 *
	 * @param   array   $dataFocus  Array of the values of diffrent focus point
	 *
	 * @param   string  $filePath   Full path for the file
	 *
	 * @return  boolean
	 *
	 * @since 4.0.0
	 */
	public function setFocus($dataFocus,$filePath)
	{
		$newEntry = array(
			$filePath => array(
				"data-focus-top" => $dataFocus['data-focus-top'],
				"data-focus-left" => $dataFocus['data-focus-left'],
				"data-focus-bottom" => $dataFocus['data-focus-bottom'],
				"data-focus-right" => $dataFocus['data-focus-right']
			)
		);

		if (filesize(static::$dataLocation))
		{
			$openFileRead = fopen(static::$dataLocation, "r");

			$prevData = fread($openFileRead, filesize(static::$dataLocation));

			fclose($openFileRead);

			$prevData = json_decode($prevData, true);

			$prevData[$filePath]["data-focus-top"] = $dataFocus['data-focus-top'];
			$prevData[$filePath]["data-focus-left"] = $dataFocus['data-focus-left'];
			$prevData[$filePath]["data-focus-bottom"] = $dataFocus['data-focus-bottom'];
			$prevData[$filePath]["data-focus-right"] = $dataFocus['data-focus-right'];

			$openFileWrite = fopen(static::$dataLocation, "w");

			fwrite($openFileWrite, json_encode($prevData));

			fclose($openFileWrite);
		}
		else
		{
			$openFile = fopen(static::$dataLocation, "w");

			$JSONdata = json_encode($newEntry);

			fwrite($openFile, $JSONdata);

			fclose($openFile);
		}

		return true;

	}

	/**
	 * Function to get the focus point
	 *
	 * @param   string  $imgSrc  Image Path
	 *
	 * @return  array
	 *
	 * @since 4.0.0
	 */
	public function getFocus($imgSrc)
	{
		$openFileRead = fopen(static::$dataLocation, "r");

		if (!filesize(static::$dataLocation))
		{
			return false;
		}

		$prevData = fread($openFileRead, filesize(static::$dataLocation));

		fclose($openFileRead);

		$prevData = json_decode($prevData, true);

		if (array_key_exists($imgSrc, $prevData))
		{
			return json_encode($prevData[$imgSrc]);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get the imageSrc.
	 *
	 * index.php?option=com_media&task=adaptiveimage.setfocus&path=/images/sampledata/fruitshop/bananas_1.jpg
	 *
	 * @return  string
	 *
	 * @since   4.0.0
	 */
	public function imageSrc()
	{
		$src = $this->input->getString('path');

		return $src;
	}

	/**
	 * Check whether the file exist
	 *
	 * @param   string  $dataLocation  location of storage file
	 *
	 * @return  boolean
	 *
	 * @since 4.0.0
	 */
	private function checkStorage( $dataLocation )
	{
		if (!file_exists($dataLocation))
		{
			touch($dataLocation);

			return true;
		}
	}

}
