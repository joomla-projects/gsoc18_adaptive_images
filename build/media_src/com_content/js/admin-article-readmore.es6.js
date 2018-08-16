/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
(() => {
  'use strict';
  const options = window.Joomla.getOptions('xtd-readmore');

  window.insertReadmore = (editor) => {
    if (!options) {
      // Something went wrong!
      throw new Error('XTD Button \'read more\' not properly initialized');
    }

    const content = window.Joomla.editors.instances[editor].getValue();

    if (content && !content.match(/<hr\s+id=("|')system-readmore("|')\s*\/*>/i)) {
      Joomla.editors.instances[editor].replaceSelection('<hr id="system-readmore">');
    } else {
      // TODO replace with joomla-alert
      alert(options.exists);
      return false;
    }
    return true;
  };
})();
