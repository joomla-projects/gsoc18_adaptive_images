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
use Joomla\CMS\AdaptiveImage\JSONFocusStore;

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
		HTMLHelper::_('script', 'media/plg_media-action_smartcrop/js/responsive-images.min.js', ['version' => 'auto', 'relative' => false]);

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
	 * @param   mixed   &$params  Additional parameters.
	 *
	 * @return  boolean  True on success.
	 */
	protected function insertFocus(&$text, &$params)
	{
		// Regular Expression from <img> tags in article
		$searchImage = '(<img[^>]+>)';

		// Match pattern and return array into $images
		preg_match_all($searchImage, $text, $images);

		$storage = new JSONFocusStore;

		// Process image one by one
		foreach ($images[0] as $key => $image)
		{
			// Clean path of the image and store in $src[1].
			preg_match('(src="([^"]+)")', $image, $src);

			// Image Path
			$imgPath = "/" . $src[1];
			
			// Takeing Focus Points
			$data = $storage->getFocus($imgPath);

			// If no data is found exit loop
			if ($data)
			{
				$data = json_decode($data, true);

				// Inserting data into respective attibutes
				$focus = "focus-x		=	\"" . $data['box-left'] . "\"
						focus-y			=	\"" . $data['box-top'] . "\"
						focus-width		=	\"" . $data['box-width'] . "\"
						focus-height	=	\"" . $data['box-height'] . "\"
						class = \"adaptiveimg\"/>";

				// Adding attributes in the <img> tag
				$newTag = str_replace("/>", $focus, $image);

				// Replaceing the previous <img> tag with new one.
				$text = str_replace($image, $newTag, $text);
			}

		}

		return true;
	}
}
