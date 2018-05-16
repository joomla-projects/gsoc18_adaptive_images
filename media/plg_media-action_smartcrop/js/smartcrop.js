/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
Joomla = window.Joomla || {};

Joomla.MediaManager = Joomla.MediaManager || {};
Joomla.MediaManager.Edit = Joomla.MediaManager.Edit || {};

(function () {
	"use strict";
    var canvas = document.getElementById("image-preview"),
        context = canvas.getContext("2d"),
        rect = {},
        drag = false,
        img = document.createElement("img"),
        imageName,
        left = 0,
        top = 0, 
        right = 1,
        bottom = 1,
        canvasWidth = canvas.width,
        canvasHeight = canvas.height,
        imgWidth = canvasWidth, 
        imgHeight = canvasHeight,
        clearCanvas = function() {
            // context.clearRect(0, 0, canvasWidth, canvasHeight);
	        context.fillStyle = "#ffffff";
	        context.fillRect(0, 0, canvasWidth, canvasHeight);
        };

    function drawImg() {
        clearCanvas();
        context.drawImage(img, 0, 0, imgWidth, imgHeight);
    }

    function updateResult(name, left, top, right, bottom) {
    	document.getElementById('jform_data_focus_top').value = top;
		document.getElementById('jform_data_focus_left').value = left;
		document.getElementById('jform_data_focus_bottom').value = bottom;
		document.getElementById('jform_data_focus_right').value = right;
    }

    // Image for loading	
    img.addEventListener("load", function() {
        var ratio = img.naturalWidth / img.naturalHeight;
        if (img.naturalHeight > canvasHeight && ratio < 1) {
            imgWidth = canvasHeight * ratio;
            imgHeight = canvasHeight;
        } else if (img.naturalWidth > canvasWidth && ratio > 1) {
            imgWidth = canvasWidth;
            imgHeight = canvasWidth / ratio;
        } else {
            imgWidth = img.naturalWidth;
            imgHeight = img.naturalHeight;
        }
        drawImg();
    }, false);

    // To enable drag and drop
    canvas.addEventListener("dragover", function(evt) {
        evt.preventDefault();
    }, false);

    // Handle dropped image file - only Firefox and Google Chrome
    canvas.addEventListener("drop", function(evt) {
        var files = evt.dataTransfer.files;
        if (files.length > 0) {
            var file = files[0];
            if (typeof FileReader !== "undefined" && file.type.indexOf("image") != -1) {
                var reader = new FileReader();
                // Note: addEventListener doesn't work in Google Chrome for this event
                reader.onload = function(evt) {
                    img.src = evt.target.result;
                };
                reader.readAsDataURL(file);
                imageName = file.name;
                updateResult(imageName, 0, 0, 1, 1);
            }
        }
        evt.preventDefault();
    }, false);

    // Detect mousedown
    canvas.addEventListener("mousedown", function(e) {
        rect.startX = e.layerX;
        rect.startY = e.layerY;
        drag = true;
    }, false);

    // Detect mouseup
    canvas.addEventListener("mouseup", function(e) {
        drag = false;
    }, false);

    // Draw, if mouse button is pressed
    canvas.addEventListener("mousemove", function(e) {
        if (drag) {
            drawImg();
            
            rect.w = (e.layerX) - rect.startX;
            rect.h = (e.layerY) - rect.startY;
            left = rect.startX / imgWidth;
            top = rect.startY / imgHeight;
            right = (rect.w + rect.startX) / imgWidth;
            bottom = (rect.h + rect.startY) / imgHeight;
            if (imageName) {
            	updateResult(imageName, left.toFixed(2), top.toFixed(2), right.toFixed(2), bottom.toFixed(2));
			}
            context.fillStyle = "rgba(255, 0, 0, 0.3)";
            context.fillRect(rect.startX, rect.startY, rect.w, rect.h);
        }
    }, false);

    // Save image
    /*var saveImage = document.getElementById("button");
    saveImage.addEventListener("click", function(evt) {
        window.open(canvas.toDataURL("image/png"));
        evt.preventDefault();
    }, false);*/
})();