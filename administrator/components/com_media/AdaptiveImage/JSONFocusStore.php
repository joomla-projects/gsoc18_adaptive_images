<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\AdaptiveImage;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\Media\Administrator\AdaptiveImage\FocusStoreInterface;

\JLoader::import('joomla.filesystem.file');

/**
 * Focus Store Class
 *
 * Used to set the focus point and save it into filesystem
 *
 * Used to get the focus point while rendering the page on the frontend
 *
 * @since  4.0.0
 */
class JSONFocusStore implements FocusStoreInterface
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
	 * Checks the storage at the initilization of the class
	 * 
	 * @since 4.0.0
	 */
	public function __construct()
	{
		$this->checkStorage(static::$dataLocation);
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
	 * Check whether the file exist
	 *
	 * @param   string  $dataLocation  location of storage file
	 * 
	 * @return  boolean
	 *
	 * @since 4.0.0
	 */
	private function checkStorage($dataLocation)
	{
		if (!file_exists($dataLocation))
		{
			touch($dataLocation);
		}

		return true;
	}
}
