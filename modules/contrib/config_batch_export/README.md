CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

This small module allows to download configs archive on slow VPS or hostings
with time http timeouts.

For example, if your site is using a CDN the CDN can limit timeout of server
response to 30 seconds. If you have a lot of config files, the process of the
archive generation can take long time. This module adds a new button for config
export in batch process, which allows to prevent getting any timeout errors or
etc.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/config_batch_export

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/config_batch_export


REQUIREMENTS
------------

This module requires no modules outside of Drupal core.


INSTALLATION
------------

 * Install the Config batch export module as you would normally install
   a contributed Drupal module.
   Visit https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

    1. Navigate to Administration > Extend and enable the module.
    2. Navigate to Administration > Configuration > Development > Configuration >
       Full Export and there will be a new button "Export in batch".
    3. Click a button and wait until it is finished.
    4. A status message with a download link will be displayed. In order to download
       configs file click this link.


MAINTAINERS
-----------

 * Jazin Bazin (Cadila) - https://www.drupal.org/u/cadila

Supporting organizations:

 * Smile - https://www.drupal.org/smile
