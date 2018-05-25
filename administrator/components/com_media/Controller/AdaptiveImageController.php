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
use Joomla\CMS\Response\JsonResponse;

\JLoader::import('joomla.filesystem.file');

/**
 * 
 * Interface for the Adaptive Image Class
 * 
 */
/*
interface AdaptiveImageInterface
{
	public function setFocus($imageSrc);
	public function getFocus($imageSrc, $dataFocus);
}
*/
/**
 * Adaptive Image Controller Class
 * 
 * Used to get the focus point and save it into filesystem
 * 
 * Used to set the focus point while rendering the page on the frontend
 *
 * @since  4.0.0
 */
class AdaptiveImageController extends BaseController // implements AdaptiveImageInterface
{

	protected $dataLocation = JPATH_PLUGINS . '/media-action/smartcrop/data/focus.json';

	public function execute($task)
	{
		$dataFocus = array(
			"data-focus-top" => 0.5,
			"data-focus-left" => 0.4,			
			"data-focus-bottom" => 0.3,
			"data-focus-right" => 0.2
		);

		$filePath = $this->imageSrc();

		$this->setFocus($dataFocus,$filePath);
	}


	/** 
	 * 
	 * Function to set the focus point
	 * 
	 * @param array $dataFocus Array of the values of diffrent focus point
	 * 
	 * @param string $filePath Full path for the file
	 * 
	 * @return boolean
	 * 
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
		
		if (filesize($this->dataLocation)){

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
	 * 
	 * Function to get the focus point
	 * 
	*/

	public function getFocus($imgSrc)
	{

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
	private function imageSrc()
	{
		$src = $this->input->getString('path');

		return $src;
	}
	
}