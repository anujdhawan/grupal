<?php
// $Id$

/**
 * Set include_path (temporarily)
 */
$incPath = get_include_path();
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . $incPath);

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata
 */
//Zend_Loader::loadClass('Zend_Gdata', dirname(__FILE__));
//require_once 'Zend/Gdata.php';

/**
 * @see Zend_Gdata_ClientLogin
 */
Zend_Loader::loadClass('Zend_Gdata_ClientLogin', dirname(__FILE__));

/**
 * @see Zend_Gdata_Gapps
 */
Zend_Loader::loadClass('Zend_Gdata_Gapps', dirname(__FILE__));

/**
 * Restore include_path
 */
set_include_path($incPath);

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using the ClientLogin credentials supplied.
 *
 * @param  string $user The username, in e-mail address format, to authenticate
 * @param  string $pass The password for the user specified
 * @return Zend_Http_Client
 */
function getClientLoginHttpClient($user, $pass) 
{
  $service = Zend_Gdata_Gapps::AUTH_SERVICE_NAME;
  $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
  return $client;
}
?>