<?php
/**
 * @package     Joomla
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\Content\Site\Interfaces\AdaptiveImageInterface;

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
	protected $dataLocation = JPATH_PLUGINS . '/media-action/smartcrop/focus.json';
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

		$this->checkStorage($this->dataLocation);

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
		elseif ($task == "getfocus")
		{
			$this->getFocus($filePath);
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

		if (filesize($this->dataLocation))
		{
			$openFileRead = fopen($this->dataLocation, "r");

			$prevData = fread($openFileRead, filesize($this->dataLocation));

			fclose($openFileRead);

			$prevData = json_decode($prevData, true);

			$prevData[$filePath]["data-focus-top"] = $dataFocus['data-focus-top'];
			$prevData[$filePath]["data-focus-left"] = $dataFocus['data-focus-left'];
			$prevData[$filePath]["data-focus-bottom"] = $dataFocus['data-focus-bottom'];
			$prevData[$filePath]["data-focus-right"] = $dataFocus['data-focus-right'];

			$openFileWrite = fopen($this->dataLocation, "w");

			fwrite($openFileWrite, json_encode($prevData));

			fclose($openFileWrite);
		}
		else
		{
			$openFile = fopen($this->dataLocation, "w");

			$JSONdata = json_encode($newEntry);

			fwrite($openFile, $JSONdata);

			fclose($openFile);
		}

		return true;

	}

	/**
	 * Function to get the focus point
	 * index.php?option=com_media&task=adaptiveimage.getfocus&path=/images/sampledata/fruitshop/bananas_1.jpg
	 *
	 * @param   string  $imgSrc  Image Path
	 *
	 * @return  boolean
	 *
	 * @since 4.0.0
	 */
	public function getFocus($imgSrc)
	{
		$openFileRead = fopen($this->dataLocation, "r");

		if (!filesize($this->dataLocation))
		{
			return false;
		}

		$prevData = fread($openFileRead, filesize($this->dataLocation));

		fclose($openFileRead);

		$prevData = json_decode($prevData, true);

		if (array_key_exists($imgSrc, $prevData))
		{
			echo json_encode($prevData[$imgSrc]);
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
