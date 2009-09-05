<?php
/* 
 * grupal - Google Apps integrations for Drupal
 * Copyright © 2009 Sam Johnston <samj@samj.net> http://samj.net/
 *                  Australian Online Solutions Pty Ltd http://www.aos.net.au/
 * 
 * $Id: grupal.admin.inc 45 2009-03-09 19:00:29Z samj@samj.net $
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once('saml2.php');

/**
 * Menu callback for single sign in service.
 */
function grupal_single_sign_in() {
  if (!(variable_get('grupal_enable_sso', '0') && variable_get('grupal_enable_single_sign_in', '1'))) {
    drupal_set_message('This service is not enabled.', 'error');
    drupal_access_denied();
  }

  global $user;
	
  if (!$user->name) {
	drupal_set_message(t('You must be logged in before accessing Google Apps.'), 'warning');
	drupal_goto();
  }

  if (!user_access('use google apps')) {
    drupal_access_denied();
  }

  if (! $html = samlResponse($user->name, variable_get('grupal_domain', ''),
    variable_get('grupal_sso_certificate', ''), variable_get('grupal_sso_private_key', ''))) {
    drupal_set_message(t('There was a problem generating a SAML assertion.'), 'error');
    drupal_goto();
  }

  return $html;
}

/**
 * Menu callback for single sign out service.
 */
function grupal_single_sign_out() {
  if (!(variable_get('grupal_enable_sso', '0') && variable_get('grupal_enable_single_sign_out', '1'))) {
	// No point confusing users returning from Google Apps with error message.
    // drupal_set_message('This service is not enabled.', 'error');
    // drupal_access_denied();
    drupal_goto();
  }

  global $user;
	
  if ($user->name) {
    drupal_goto('logout');
  } else {
	drupal_goto();
  }
}

/**
 * Menu callback for password changes.
 */
function grupal_password() {
  if (!(variable_get('grupal_enable_sso', '0') && variable_get('grupal_enable_password', '1'))) {
    drupal_set_message('This service is not enabled.', 'error');
    drupal_access_denied();
  }

  global $user;
	
  if ($user->uid) {
    drupal_goto("user/" . $user->uid . "/edit");
  } else {
	drupal_access_denied();
  }
}
