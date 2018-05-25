/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
Joomla = window.Joomla || {};

Joomla.MediaManager = Joomla.MediaManager || {};
Joomla.MediaManager.Edit = Joomla.MediaManager.Edit || {};

(function () {
	"use strict";
    var initSmartCrop = function (mediaData) {
		var image = document.getElementById('image-preview');
		
		// Initiate the cropper for gathering the focus point
		Joomla.MediaManager.Edit.smartcrop.cropper = new Cropper(image, { 
			viewMode: 1,
			responsive: false,
			restore: true,
			autoCrop: false,
			movable: true,
			zoomable: false,
			rotatable: false,
			autoCropArea: 0,
			minContainerWidth: image.offsetWidth,
			minContainerHeight: image.offsetHeight,
			crop: function (e) {

				// Top, Bottom, Left, right are the data focus points
                var canvas_data = this.cropper.getCropBoxData();
                Joomla.MediaManager.Edit.smartcrop.cropper.top = (canvas_data.top / image.naturalHeight).toFixed(2);
                Joomla.MediaManager.Edit.smartcrop.cropper.bottom = ((canvas_data.height + canvas_data.top) / image.naturalHeight).toFixed(2);
                Joomla.MediaManager.Edit.smartcrop.cropper.left = (canvas_data.left / image.naturalWidth).toFixed(2);
				Joomla.MediaManager.Edit.smartcrop.cropper.right = ((canvas_data.width + canvas_data.left) / image.naturalWidth).toFixed(2);
				
				// Setting the computed focus point into the input fields
				document.getElementById('jform_data_focus_top').value = Joomla.MediaManager.Edit.smartcrop.cropper.top;
				document.getElementById('jform_data_focus_bottom').value = Joomla.MediaManager.Edit.smartcrop.cropper.bottom;
				document.getElementById('jform_data_focus_left').value = Joomla.MediaManager.Edit.smartcrop.cropper.left;
				document.getElementById('jform_data_focus_right').value = Joomla.MediaManager.Edit.smartcrop.cropper.right;

				// Manageing image extension 
				var format = Joomla.MediaManager.Edit.original.extension === 'jpg' ? 'jpeg' : Joomla.MediaManager.Edit.original.extension;

				// Takeing the value of the quality
				var quality = document.getElementById('jform_crop_quality').value;

				// Notify the app that a change has been made
				window.dispatchEvent(new Event('mediaManager.history.point'));
			}
		});
    }

    // Register the Events
	Joomla.MediaManager.Edit.smartcrop = {
		Activate: function (mediaData) {
			// Initialize
			initSmartCrop(mediaData);
		},
		Deactivate: function () {
            if (!Joomla.MediaManager.Edit.smartcrop.cropper) {
				return;
			}
			// Destroy the instance
			Joomla.MediaManager.Edit.smartcrop.cropper.destroy();
		}
	};
})();