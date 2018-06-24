<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\CMS\AdaptiveImage;
defined('_JEXEC') or die;

use Joomla\Image\Image;
/**
 * Used for cropping of the images around
 * the focus points.
 *
 * @since  4.0.0
 */
class SmartCrop
{
    public static $dataLocation = "/images/.cache";
    public static $imgPath;
    public static $image;

    public function __construct($src)
    {
        $this->image = new Image($src);
        $this->imgPath = $src;
    }
    public function compute($dataFocus, $finalDimentions)
    {
        $fx = $dataFocus['box-left'];
        $fy = $dataFocus['box-top'];
        $fwidth = $dataFocus['box-width'];
        $fheight = $dataFocus['box-height'];

        $twidth = $finalDimentions['width'];
        $theight = $finalDimentions['height'];

        $mwidth = $this->image->getWidth();
        $mheight = $this->image->getHeight();

        if($twidth<$fwidth || $theight<$fheight)
        {
            //Scale down the selection.
            $this->image->resize($fwidth, $fheight);
        }
        elseif ($twidth>=$mwidth || $theight>=$mheight)
        {
            //show original Image do nothing
            $fx=0;
            $fy=0;
        }
        else
        {
            $diff_x = ($twidth - $fwidth) / 2;
            $fx = $fx - $diff_x;
            $x2 = $fx + $twidth;
            if ($x2>$mwidth)
            {
                $fx = $fx - ($x2-$mwidth);
            }
            elseif ($fx<0)
            {
                $fx=0;
            }
            $diff_y = ($theight - $fheight)/2;
            $fy = $fy - $diff_y;
            $y2 = $fy + $theight;
            if ($y2>$mheight)
            {
                $fy = $fy - ($y2-$mheight);
            }
            elseif($fy<0)
            {
                $fy=0;
            }
            $this->image->crop($fwidth, $fheight, $fx, $fy);
        }
        return true;
    }
}