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
    public $dataLocation = "../images/.cache";
    public $imgPath;
    public $image;

    public function __construct($imgPath)
    {
        $this->image = new Image($imgPath);
        $this->imgPath = $imgPath;
        $this->checkDir();
    }
    public function compute($dataFocus, $finalWidth)
    {
        $fx = $dataFocus["box-left"];
        $fy = $dataFocus["box-top"];
        $fwidth = $dataFocus["box-width"];
        $fheight = $dataFocus["box-height"];

        $mwidth = $this->image->getWidth();
        $mheight = $this->image->getHeight();

        $twidth = $finalWidth;
        $theight = $twidth*$mheight/$mwidth;

        if($twidth<$fwidth || $theight<$fheight)
        {
            //Scale down the selection.
            $finalImage = $this->image->crop($fwidth, $fheight, $fx, $fy);
            $finalImage = $finalImage->resize($twidth,$theight);
            $imgName = explode('/', $this->imgPath);
            $imgName = "/" . $twidth . "_" . $imgName[max(array_keys($imgName))];
            $path = $this->dataLocation . $imgName;
            echo $path;
            $finalImage->toFile($path);
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
            $finalImage = $this->image->crop($twidth, $theight, $fx, $fy);
            $imgName = explode('/', $this->imgPath);
            $imgName = "/" . $twidth . "_" . $imgName[max(array_keys($imgName))];
            $path = $this->dataLocation . $imgName;
            $finalImage->toFile($path);
        }
        return true;
    }
    public function checkDir()
    {
        if(!is_dir($this->dataLocation))
        {
            mkdir($this->dataLocation, 0777);
        }
        return true;
    }
}