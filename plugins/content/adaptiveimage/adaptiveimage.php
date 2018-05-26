<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.adaptiveimage
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Adaptive Image Plugin
 *
 * @since  4.0
 */
class PlgContentAdaptiveImage extends CMSPlugin
{

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 *
	 * @since  4.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Plugin that inserts focus points into the image.
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   mixed    &$row     An object with a "text" property.
	 * @param   mixed    &$params  Additional parameters.
	 * @param   integer  $page     Optional page number. Unused. Defaults to zero.
	 *
	 * @return  boolean	True on success.
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{

		// Add ResponsifyJS into the client page
		//HTMLHelper::_('script', 'media/vendor/responsifyjs/responsify.js', ['version' => 'auto', 'relative' => false]);

		// Don't run this plugin when the content is being indexed
		if ($context === 'com_finder.indexer')
		{
			return true;
		}

		if (is_object($row))
		{
			return $this->insertFocus($row->text, $params);
		}

		return $this->insertFocus($row, $params);
	}

	/**
	 * Inserts focus points into the image.
	 *
	 * @param   string  &$text    HTML string.
	 * @param   mixed   &$params  Additional parameters. Parameter "mode" (integer, default 1)
	 *                             replaces addresses with "mailto:" links if nonzero.
	 *
	 * @return  boolean  True on success.
	 */
	protected function insertFocus(&$text, &$params)
	{
		// Regular Expression from <img> tags in article
		$searchImage = '(<img[^>]+>)';

		// Match pattern and return array into $images
		preg_match_all($searchImage, $text, $images);

		// Process image one by one
		foreach($images[0] as $key => $image)
		{
			// clean path of the image and store in $src[1].
			preg_match('(src="([^"]+)")', $image, $src);

			// URL of the adaptive image controller
			$getUrl = JURI::base() . 'index.php?option=com_content&task=adaptiveimage.getfocus&path=/'.$src[1];
			
			// Getting the data returned by the controller
			$data = file_get_contents($getUrl);

			// If no data is found exit loop
			if ( !$data )
			{
				break;
			}

			// Converts JSON data into php array
			$data = json_decode($data, true);

			// Inserting data into respective attibutes
			$focus = "data-focus-left	=	\"".$data['data-focus-left']	."\"
					data-focus-top		=	\"".$data['data-focus-top']		."\"
					data-focus-right	=	\"".$data['data-focus-right']	."\" 
					data-focus-bottom	=	\"".$data['data-focus-bottom']	."\" />";

			// Adding attributes in the <img> tag
			$newTag = str_replace("/>", $focus, $image);

			// Replaceing the previous <img> tag with new one.
			$text = str_replace($image, $newTag, $text);
		}
		
		return true;
	}
}
