<?php
// $Id$

/**
 * Form API callback to validate the upload settings form.
 */
function grupal_admin_settings_validate($form, &$form_state) {
  /*
  $email_quota = $form_state['values']['grupal_email_quota'];
  if (!is_numeric($email_quota) || ($email_quota <= 0)) {
    form_set_error('grupal_email_quota', t('The email quota must be numeric.', array()));
  }
  */
}

/**
* Admin form for this module
* @return system settings form
*/
function grupal_admin_settings() {
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
  $form['global']['grupal_admin_email'] = array(
    '#type' => 'textfield',
    '#title' => t('Google Apps admin email'),
    '#default_value' => variable_get('grupal_admin_email', 'admin@example.com'),
    '#size' => 32,
    '#maxlength' => 128,
    '#description' => t("Google Apps administrator email.")
  );
  $form['global']['grupal_admin_pass'] = array(
    '#type' => 'password',
    '#title' => t('Google Apps admin password'),
    '#default_value' => variable_get('grupal_admin_pass', ''),
    '#size' => 16,
    '#maxlength' => 100,
    '#description' => t("Google Apps administrator password to be used to create accounts.")
  );
  $form['global']['grupal_account_status'] = array(
    '#type' => 'radios',
    '#title' => t('Google Apps account status on creation'),
    '#default_value' => variable_get('grupal_account_status', 1),
    '#options' => array(t('Disabled'), t('Enabled')),
    '#description' => t("Specify whether accounts should be enabled or suspended on creation.")
  );

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

  $form['#validate'] = array('grupal_admin_settings_validate');

  return system_settings_form($form);
} // function grupal_admin
