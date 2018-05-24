<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.smart-crop
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Media Manager Smart Crop Action
 *
 * @since  4.0.0
 */
class PlgMediaActionSmartCrop extends \Joomla\Component\Media\Administrator\Plugin\MediaActionPlugin
{
	public function __construct()
	{
		echo "2";
	}

	public function onContentPrepare($context, &$row, $params, $page = 0)
	{
		echo "1";
	}

	/*
	* @Override the parent function onContentPrepareForm
	*
	* @param   Form       $form  The form
	* @param   \stdClass  $data  The data
	*
	* @return  void
	*
	* @since   4.0.0
	*/

	public function onContentPrepareForm(Form $form, $data)
	{
		// Check if it is the right form
		if ($form->getName() != 'com_media.file')
		{
			return;
		}

		// Loading the JS file from the parent class
		parent::loadJs();

		// Loading the CSS file from the parent class
		parent::loadCss();

		// The file with the params for the edit view
		$paramsFile = JPATH_PLUGINS . '/media-action/' . $this->_name . '/form/' . $this->_name . '.xml';

		// When the file exists, load it into the form
		if (file_exists($paramsFile))
		{
			$form->loadFile($paramsFile);
		}
		
		// $this->saveDataFocusPoint($form->getValue("jform_smartcrop_quality","","hello"));
	}

	public function onContentPrepareData($context,$data){
		print_r($context);

	}

	/*
	* Defineing the variables
	*
	* @var array
	*/
	protected $data_focus = array(
		'data-focus-top' => '',
		'data-focus-left' => '',
		'data-focus-bottom' => '',
		'data-focus-right' => ''
	);

	/*
	* Saveing the Data Focus point into the JSON file.
	*/

	public function saveDataFocusPoint($form){
		//print_r($form);
	}

	/*
	* Replaceing img tags with the tags with the data-focus points.
	*/

	public function replaceImg(&$article){
		$images = array();
		preg_match_all('/{img src=(.*?)}/is', $article->text, $images);
		echo "HI...";
		print_r($images);
	}
}
