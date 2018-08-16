/**
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
Joomla = window.Joomla || {};

((Joomla, document) => {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    const target = window.parent.document.getElementById('target-association');
    const links = [].slice.call(document.querySelectorAll('.select-link'));

    links.forEach((item) => {
      item.addEventListener('click', (event) => {
        target.src = `${target.getAttribute('data-editurl')}&task=${target.getAttribute('data-item')}.edit&id=${parseInt(event.target.getAttribute('data-id'), 10)}`;
        window.parent.Joomla.Modal.getCurrent().close();
      });
    });
  });
})(Joomla, document);
