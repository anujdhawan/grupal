<?php
/* 
 * grupal - Google Apps integrations for Drupal
 * Copyright © 2009 Sam Johnston <samj@samj.net> http://samj.net/
 *                  Australian Online Solutions Pty Ltd http://www.aos.net.au/
 * 
 * $Id$
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

/**
 * Form API callback to validate the upload settings form.
 */
function grupal_admin_settings_validate($form, &$form_state) {
  // TODO: Validate Google Apps credentials
  /*
  $email_quota = $form_state['values']['grupal_email_quota'];
  if (!is_numeric($email_quota) || ($email_quota <= 0)) {
    form_set_error('grupal_email_quota', t('The email quota must be numeric.',
      array()));
  }
  */
  if (!$form_state['values']['grupal_admin_pass']) {
    form_set_error(('grupal_admin_pass'), t('For security reasons you must " .
      "always specify the administrator password.', array()));
  }
}

/**
* Admin form for this module
* @return system settings form
*/
function grupal_admin_settings() {
  $form['credentials'] = array(
    '#type' => 'fieldset',
    '#title' => t('Google Apps service credentials'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['credentials']['grupal_admin_email'] = array(
    '#type' => 'textfield',
    '#title' => t('Google Apps admin email'),
    '#default_value' =>
      variable_get('grupal_admin_email', 'admin@example.com'),
    '#size' => 32,
    '#maxlength' => 128,
    '#description' => t("Google Apps administrator email.")
  );
  $form['credentials']['grupal_admin_pass'] = array(
    '#type' => 'password',
    '#title' => t('Google Apps admin password'),
    '#default_value' => variable_get('grupal_admin_pass', ''),
    '#size' => 16,
    '#maxlength' => 100,
    '#description' =>
      t("Google Apps administrator password to be used to create accounts.")
  );
  $form['global'] = array(
    '#type' => 'fieldset',
    '#title' => t('Google Apps service settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['global']['grupal_enabled'] = array(
    '#type' => 'radios',
    '#title' => t('Google Apps enabled'),
    '#default_value' => variable_get('grupal_enabled', 0),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t("Google Apps integrations enabled or disabled.")
  );
  $form['global']['grupal_domain'] = array(
    '#type' => 'textfield',
    '#title' => t('Google Apps domain'),
    '#default_value' => variable_get('grupal_domain', 'example.com'),
    '#size' => 32,
    '#maxlength' => 255,
    '#description' => t("Google Apps domain, may differ from Drupal domain.")
  );
  $form['global']['grupal_status_created'] = array(
    '#type' => 'radios',
    '#title' => t('Google Apps account status on creation'),
    '#default_value' => variable_get('grupal_status_created', 2),
    '#options' => array(t('Ignore'),t('Suspend'), t('Enable')),
    '#description' =>
      t("Specify whether accounts should be enabled or suspended on creation.")
  );
  $form['global']['grupal_status_deleted'] = array(
    '#type' => 'radios',
    '#title' => t('Google Apps account status on deletion'),
    '#default_value' => variable_get('grupal_status_deleted', 1),
    '#options' => array(t('Ignore'), t('Suspend'), t('Delete')),
    '#description' =>
      t("Specify whether accounts should be suspended or deleted on deletion.")
  );
  $form['global']['grupal_update_password'] = array(
    '#type' => 'radios',
    '#title' => t('Google Apps update password'),
    '#default_value' => variable_get('grupal_update_password', 1),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' =>
      t("Specify whether account passwords should be updated when changed.")
  );
  $form['global']['grupal_update_suspend'] = array(
    '#type' => 'radios',
    '#title' => t('Google Apps update suspend status'),
    '#default_value' => variable_get('grupal_update_suspend', 1),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t("Specify whether accounts should be suspended when " .
      "blocked (and vice versa).")
  );
  $form['global']['grupal_change_username'] = array(
    '#type' => 'radios',
    '#title' => t('Permit Drupal username changes'),
    '#default_value' => variable_get('grupal_change_username', 0),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t("Specify whether Drupal username changes are " .
      "permitted (which can break links between users).")
  );
  $form['sso'] = array(
    '#type' => 'fieldset',
    '#title' => t('Google Apps Single Sign On (SSO)'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['sso']['grupal_enable_sso'] = array(
    '#type' => 'radios',
    '#title' => t('Enable SAML 2 Single Sign On (SSO)'),
    '#default_value' => variable_get('grupal_enable_sso', 0),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t("Specify whether Single Sign On (SS) services should be enabled globally.")
  );
  $form['sso']['grupal_enable_single_sign_in'] = array(
    '#type' => 'radios',
    '#title' => t('Enable SAML 2 single sign in service'),
    '#default_value' => variable_get('grupal_enable_single_sign_in', 1),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t("Specify whether Drupal should respond to sign in requests at grupal/signin.")
  );
  $form['sso']['grupal_enable_single_sign_out'] = array(
    '#type' => 'radios',
    '#title' => t('Enable SAML 2 single sign out service'),
    '#default_value' => variable_get('grupal_enable_single_sign_out', 1),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t("Specify whether Drupal should respond to sign out requests at grupal/signout.")
  );
  // TODO: generate automatically: openssl_pkey_export(openssl_pkey_new(private_key_bits=1024))
  // TODO: download PEM file
  $form['sso']['grupal_enable_password'] = array(
    '#type' => 'radios',
    '#title' => t('Enable password change URL'),
    '#default_value' => variable_get('grupal_enable_password', 1),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t("Specify whether Drupal should respond to password change requests at grupal/password.")
  );
  $form['sso']['grupal_sso_private_key'] = array(
    '#type' => 'textarea',
    '#title' => t('Single Sign On (SSO) private key'),
    '#default_value' => variable_get('grupal_sso_private_key', ''),
    '#description' => t("Private DSA/RSA key for Single Sign On (SSO) in PEM format.")
  );
  $form['sso']['grupal_sso_certificate'] = array(
    '#type' => 'textarea',
    '#title' => t('Single Sign On (SSO) public key'),
    '#default_value' => variable_get('grupal_sso_certificate', ''),
    '#description' => t("X.509 certificate for Single Sign On (SSO) in PEM format.")
  );
/* 
  $form['email'] = array(
    '#type' => 'fieldset',
    '#title' => t('Google Apps email account settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['email']['grupal_email_quota'] = array(
    '#type' => 'textfield',
    '#title' => t('Mail quota'),
    '#default_value' => variable_get('grupal_email_quota', ''),
    '#size' => 6,
    '#maxlength' => 5,
    '#description' => t('Amount of space available for mail (Mb).'),
  );
*/
  $form['#validate'] = array('grupal_admin_settings_validate');

  return system_settings_form($form);
} // function grupal_admin
