/**
* PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
* OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
**/

/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
(function (Joomla, document) {
  'use strict';

  /**
   * Javascript to insert the link
   * View element calls jSelectContact when a contact is clicked
   * jSelectContact creates the link tag, sends it to the editor,
   * and closes the select frame.
   */

  window.jSelectMenuItem = function (id, title, uri, object, link, lang) {
    var thislang = '';

    if (!Joomla.getOptions('xtd-menus')) {
      // Something went wrong!
      window.parent.Joomla.Modal.getCurrent().close();

      throw new Error('core.js was not properly initialised');
    }

    // eslint-disable-next-line prefer-destructuring
    var editor = Joomla.getOptions('xtd-menus').editor;

    if (lang !== '') {
      thislang = '&lang=';
    }

    var tag = '<a href="' + (uri + thislang + lang) + '">' + title + '</a>';

    // Insert the link in the editor
    window.parent.Joomla.editors.instances[editor].replaceSelection(tag);

    // Close the modal
    if (window.parent.Joomla && window.parent.Joomla.Modal) {
      window.parent.Joomla.Modal.getCurrent().close();
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    // Get the elements
    var elements = [].slice.call(document.querySelectorAll('.select-link'));

    elements.forEach(function (element) {
      // Listen for click event
      element.addEventListener('click', function (event) {
        event.preventDefault();
        var functionName = event.target.getAttribute('data-function');

        if (functionName === 'jSelectMenuItem') {
          // Used in xtd_contacts
          window[functionName](event.target.getAttribute('data-id'), event.target.getAttribute('data-title'), event.target.getAttribute('data-uri'), null, null, event.target.getAttribute('data-language'));
        } else {
          // Used in com_menus
          window.parent[functionName](event.target.getAttribute('data-id'), event.target.getAttribute('data-title'), null, null, event.target.getAttribute('data-uri'), event.target.getAttribute('data-language'), null);
        }

        // Close the modal
        if (window.parent.Joomla.Modal) {
          window.parent.Joomla.Modal.getCurrent().close();
        }
      });
    });
  });
})(Joomla, document);
