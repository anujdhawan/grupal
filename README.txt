grupal - Drupal® plus seamless integration with Google Apps

Copyright © 2009 Sam Johnston <samj@samj.net> - http://samj.net/
                 Australian Online Solutions Pty Ltd - http://www.aos.net.au/

Supports
========
 * Creating users in Google Apps when created in Drupal (suspended or enabled)
 * Suspending users in Google Apps when blocked in Drupal
 * Synchronising passwords in Google Apps when updated in Drupal
 * Suspending or deleting users in Google Apps when deleted in Drupal 

Features
========
 * Uses Zend Framework for Google Apps APIs
 * Open Source (GPLv2) licensing
 * Commercial licensing and support for both Grupal and Google Apps available
   from Australian Online Solutions (a Google Solution Provider). 
 * Performance optimisations only talk to Google when absolutely necessary
 * Security feature requires Google Apps admin password for all changes
 * Ability to intercept and prevent username updates (which break links) 
 * Integrated Single Sign On (SSO)

Warning
=======
 * The Google Apps Provisioning API on which this code depends is not available
   in the free Standard Edition.
 * For security reasons ensure that AllowOverride is configured appropriately
   such that the .htaccess files take effect

Requires
========
 * Drupal 6.x
 * Google Apps
  o Premier Edition
  o Education Edition
  o Non-profit Edition
  o Partner Edition 

Getting Started
===============
Stable releases:
 - Download the latest tarball from http://code.google.com/p/grupal/downloads/list
 - Extract to temporary directory
 - Move grupal module directory to sites/all/modules directory
 - To generate a key for SSO use a command like:
`openssl req -new -x509 -newkey rsa:2048 -keyout grupal.key -out grupal.crt -subj '/CN=grupal/' -nodes`

Development version:
 - Obtain the latest version of Drupal (developed for Drupal 6.x)
 - Change to 'sites/all/modules' directory
 - svn co http://grupal.googlecode.com/svn/trunk/drupal/sites/all/modules/grupal grupal
 - svn up (to update to latest version)

Development
===========
 - Refer to the Drupal Module Developers Guide http://drupal.org/node/508
 - Review the tutorial at http://drupal.org/node/206753
 - Review demos at http://framework.zend.com/svn/framework/standard/trunk/demos/Zend/Gdata/

References
==========
 - Google Code Site: http://code.google.com/p/grupal
 - Google Group: http://groups.google.com/group/grupal-discuss
 - Google Apps: http://google.com/a/
 - Sam Johnston: http://samj.net/
 - Australian Online Solutions: http://www.aos.net.au/

         Drupal® is a registered trademark of Dries Buytaert
