<?php
/**
 * grupal: demos/Zend/Gdata/Gapps.php up to "BEGIN CLI SPECIFIC CODE"
 * 
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHP sample code for the Google Calendar data API.  Utilizes the 
 * Zend Framework Gdata components to communicate with the Google API.
 * 
 * Requires the Zend Framework Gdata components and PHP >= 5.1.4
 *
 * You can run this sample both from the command line (CLI) and also
 * from a web browser.  Run this script without any command line options to 
 * see usage, eg:
 *     /usr/bin/env php Gapps.php
 *
 * More information on the Command Line Interface is available at:
 *     http://www.php.net/features.commandline
 *
 * When running this code from a web browser, be sure to fill in your 
 * Google Apps credentials below and choose a password for authentication 
 * via the web browser.
 *
 * Since this is a demo, only minimal error handling and input validation 
 * are performed. THIS CODE IS FOR DEMONSTRATION PURPOSES ONLY. NOT TO BE 
 * USED IN A PRODUCTION ENVIRONMENT.
 *
 * NOTE: You must ensure that Zend Framework is in your PHP include
 * path.  You can do this via php.ini settings, or by modifying the 
 * argument to set_include_path in the code below.
 */

// ************************ BEGIN WWW CONFIGURATION ************************

/**
 * Google Apps username. This is the username (without domain) used 
 * to administer your Google Apps account. This value is only 
 * used when accessing this demo on a web server.
 * 
 * For example, if you login to Google Apps as 'foo@bar.com.inavlid', 
 * your username is 'foo'.
 */
define('GAPPS_USERNAME', 'username');

/**
 * Google Apps domain. This is the domain associated with your 
 * Google Apps account. This value is only used when accessing this demo 
 * on a web server.
 * 
 * For example, if you login to Google Apps as foo@bar.com.inavlid, 
 * your domain is 'bar.com.invalid'.
 */
define('GAPPS_DOMAIN', 'example.com.invalid');

/**
 * Google Apps password. This is the password associated with the above 
 * username. This value is only used when accessing this demo on a 
 * web server.
 */
define('GAPPS_PASSWORD', 'your password here');

/**
 * Login password. This password is used to protect your account from 
 * unauthorized access when running this demo on a web server.
 *
 * If this field is blank, all access will be denied. A blank password 
 * field is not the same as no password (which is disallowed for 
 * security reasons).
 *
 * NOTE: While we could technically just ask the user for their Google Apps
 *       credentials, the ClientLogin API is not intended for direct use by
 *       web applications. If you are the only user of the application, this
 *       is fine--- but you should not ask other users to enter their 
 *       credentials via your web application.
 */
define('LOGIN_PASSWORD', '');

// ************************* END WWW CONFIGURATION *************************

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata
 */
Zend_Loader::loadClass('Zend_Gdata');

/**
 * @see Zend_Gdata_ClientLogin
 */
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

/**
 * @see Zend_Gdata_Gapps
 */
Zend_Loader::loadClass('Zend_Gdata_Gapps');

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

/**
 * Creates a new user for the current domain. The user will be created 
 * without admin privileges.
 * 
 * @param  Zend_Gdata_Gapps $gapps      The service object to use for communicating with the Google
 *                                      Apps server.
 * @param  boolean          $html       True if output should be formatted for display in a web browser.
 * @param  string           $username   The desired username for the user.
 * @param  string           $givenName  The given name for the user.
 * @param  string           $familyName The family name for the user.
 * @param  string           $password   The plaintext password for the user.
 * @return void
 */
function createUser($gapps, $html, $username, $givenName, $familyName, 
        $password)
{
    if ($html) {echo "<h2>Create User</h2>\n";}
    $gapps->createUser($username, $givenName, $familyName, 
        $password);
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Retrieves a user for the current domain by username. Information about 
 * that user is then output.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The desired username for the user.
 * @return void
 */
function retrieveUser($gapps, $html, $username)
{
    if ($html) {echo "<h2>User Information</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($html) {echo '<p>';}
    
    if ($user !== null) {
        echo '             Username: ' . $user->login->username;
        if ($html) {echo '<br />';}
        echo "\n";
    
        echo '           Given Name: ';
        if ($html) {
            echo htmlspecialchars($user->name->givenName);
        } else {
            echo $user->name->givenName;
        }
        if ($html) {echo '<br />';}
        echo "\n";
    
        echo '          Family Name: ';
        if ($html) {
            echo htmlspecialchars($user->name->familyName);
        } else {
            echo $user->name->familyName;
        }
        if ($html) {echo '<br />';}
        echo "\n";
    
        echo '            Suspended: ' . ($user->login->suspended ? 'Yes' : 'No');
        if ($html) {echo '<br />';}
        echo "\n";
    
        echo '                Admin: ' . ($user->login->admin ? 'Yes' : 'No');
        if ($html) {echo '<br />';}
        echo "\n";
    
        echo ' Must Change Password: ' . 
            ($user->login->changePasswordAtNextLogin ? 'Yes' : 'No');
        if ($html) {echo '<br />';}
        echo "\n";
        
        echo '  Has Agreed To Terms: ' . 
            ($user->login->agreedToTerms ? 'Yes' : 'No');
        
    } else {
        echo 'Error: Specified user not found.';
    }
    if ($html) {echo '</p>';}
    echo "\n";
}

/**
 * Retrieves the list of users for the current domain and outputs 
 * that list.
 * 
 * @param  Zend_Gdata_Gapps $gapps The service object to use for communicating with the Google Apps server.
 * @param  boolean          $html  True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveAllUsers($gapps, $html)
{
    if ($html) {echo "<h2>Registered Users</h2>\n";}
    
    $feed = $gapps->retrieveAllUsers();
    
    if ($html) {echo "<ul>\n";}
    
    foreach ($feed as $user) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $user->login->username . ' (';
        if ($html) {
            echo htmlspecialchars($user->name->givenName . ' ' . 
                $user->name->familyName);
        } else {
            echo $user->name->givenName . ' ' . $user->name->familyName;
        }
        echo ')';
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Change the name for an existing user.
 * 
 * @param  Zend_Gdata_Gapps $gapps         The service object to use for communicating with the Google
 *                                         Apps server.
 * @param  boolean          $html          True if output should be formatted for display in a web browser.
 * @param  string           $username      The username which should be updated
 * @param  string           $newGivenName  The new given name for the user.
 * @param  string           $newFamilyName The new family name for the user.
 * @return void
 */
function updateUserName($gapps, $html, $username, $newGivenName, $newFamilyName)
{
    if ($html) {echo "<h2>Update User Name</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($user !== null) {
        $user->name->givenName = $newGivenName;
        $user->name->familyName = $newFamilyName;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Change the password for an existing user.
 * 
 * @param  Zend_Gdata_Gapps $gapps       The service object to use for communicating with the Google
 *                                       Apps server.
 * @param  boolean          $html        True if output should be formatted for display in a web browser.
 * @param  string           $username    The username which should be updated
 * @param  string           $newPassword The new password for the user.
 * @return void
 */
function updateUserPassword($gapps, $html, $username, $newPassword)
{
    if ($html) {echo "<h2>Update User Password</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($user !== null) {
        $user->login->password = $newPassword;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Suspend a given user. The user will not be able to login until restored.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function suspendUser($gapps, $html, $username)
{
    if ($html) {echo "<h2>Suspend User</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($user !== null) {
        $user->login->suspended = true;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Restore a given user after being suspended.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function restoreUser($gapps, $html, $username)
{
    if ($html) {echo "<h2>Restore User</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($user !== null) {
        $user->login->suspended = false;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Give a user admin rights.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function giveUserAdminRights($gapps, $html, $username)
{
    if ($html) {echo "<h2>Grant Administrative Rights</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($user !== null) {
        $user->login->admin = true;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Revoke a user's admin rights.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function revokeUserAdminRights($gapps, $html, $username)
{
    if ($html) {echo "<h2>Revoke Administrative Rights</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($user !== null) {
        $user->login->admin = false;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Force a user to change their password at next login.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function setUserMustChangePassword($gapps, $html, $username)
{
    if ($html) {echo "<h2>Force User To Change Password</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($user !== null) {
        $user->login->changePasswordAtNextLogin = true;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Undo forcing a user to change their password at next login.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function clearUserMustChangePassword($gapps, $html, $username)
{
    if ($html) {echo "<h2>Undo Force User To Change Password</h2>\n";}
    
    $user = $gapps->retrieveUser($username);
    
    if ($user !== null) {
        $user->login->changePasswordAtNextLogin = false;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Delete the user who owns a given username.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be deleted.
 * @return void
 */
function deleteUser($gapps, $html, $username)
{
    if ($html) {echo "<h2>Delete User</h2>\n";}
    
    $gapps->deleteUser($username);
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Create a new nickname.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username to which the nickname should be assigned.
 * @param  string           $nickname The name of the nickname to be created.
 * @return void
 */
function createNickname($gapps, $html, $username, $nickname)
{
    if ($html) {echo "<h2>Create Nickname</h2>\n";}
    
    $gapps->createNickname($username, $nickname);
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Retrieve a specified nickname and output its ownership information.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $nickname The name of the nickname to be retrieved.
 * @return void
 */
function retrieveNickname($gapps, $html, $nickname)
{
    if ($html) {echo "<h2>Nickname Information</h2>\n";}
    
    $nickname = $gapps->retrieveNickname($nickname);
    
    if ($html) {echo '<p>';}
    
    if ($nickname !== null) {
        echo ' Nickname: ' . $nickname->nickname->name;
        if ($html) {echo '<br />';}
        echo "\n";
    
        echo '    Owner: ' . $nickname->login->username;
    } else {
        echo 'Error: Specified nickname not found.';
    }
    if ($html) {echo '</p>';}
    echo "\n";
}

/**
 * Outputs all nicknames owned by a specific username.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username whose nicknames should be displayed.
 * @return void
 */
function retrieveNicknames($gapps, $html, $username)
{
    if ($html) {echo "<h2>Registered Nicknames For {$username}</h2>\n";}
    
    $feed = $gapps->retrieveNicknames($username);
    
    if ($html) {echo "<ul>\n";}
    
    foreach ($feed as $nickname) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $nickname->nickname->name;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}


/**
 * Retrieves the list of nicknames for the current domain and outputs 
 * that list.
 * 
 * @param  Zend_Gdata_Gapps $gapps The service object to use for communicating with the Google
 *                                 Apps server.
 * @param  boolean          $html  True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveAllNicknames($gapps, $html)
{
    if ($html) {echo "<h2>Registered Nicknames</h2>\n";}
    
    $feed = $gapps->retrieveAllNicknames();
    
    if ($html) {echo "<ul>\n";}
    
    foreach ($feed as $nickname) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $nickname->nickname->name . ' => ' . $nickname->login->username;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Delete's a specific nickname from the current domain.
 * 
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $nickname The nickname that should be deleted.
 * @return void
 */
function deleteNickname($gapps, $html, $nickname)
{
    if ($html) {echo "<h2>Delete Nickname</h2>\n";}
    
    $gapps->deleteNickname($nickname);
    
    if ($html) {echo "<p>Done.</p>\n";}
    
}

/**
 * Create a new email list.
 * 
 * @param  Zend_Gdata_Gapps $gapps     The service object to use for communicating with the Google
 *                                     Apps server.
 * @param  boolean          $html      True if output should be formatted for display in a web browser.
 * @param  string           $emailList The name of the email list to be created.
 * @return void
 */
function createEmailList($gapps, $html, $emailList)
{
    if ($html) {echo "<h2>Create Email List</h2>\n";}
    
    $gapps->createEmailList($emailList);
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Outputs the list of email lists to which the specified address is 
 * subscribed.
 * 
 * @param  Zend_Gdata_Gapps $gapps     The service object to use for communicating with the Google
 *                                     Apps server.
 * @param  boolean          $html      True if output should be formatted for display in a web browser.
 * @param  string           $recipient The email address of the recipient whose subscriptions should
 *                                     be retrieved. Only a username is required if the recipient is a
 *                                     member of the current domain.
 * @return void
 */
function retrieveEmailLists($gapps, $html, $recipient)
{
    if ($html) {echo "<h2>Email List Subscriptions For {$recipient}</h2>\n";}
    
    $feed = $gapps->retrieveEmailLists($recipient);
    
    if ($html) {echo "<ul>\n";}
    
    foreach ($feed as $list) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $list->emailList->name;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Outputs the list of all email lists on the current domain.
 * 
 * @param  Zend_Gdata_Gapps $gapps The service object to use for communicating with the Google
 *                                 Apps server.
 * @param  boolean          $html  True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveAllEmailLists($gapps, $html)
{
    if ($html) {echo "<h2>Registered Email Lists</h2>\n";}
    
    $feed = $gapps->retrieveAllEmailLists();
    
    if ($html) {echo "<ul>\n";}
    
    foreach ($feed as $list) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $list->emailList->name;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Delete's a specific email list from the current domain.
 * 
 * @param  Zend_Gdata_Gapps $gapps     The service object to use for communicating with the Google
 *                                     Apps server.
 * @param  boolean          $html      True if output should be formatted for display in a web browser.
 * @param  string           $emailList The email list that should be deleted.
 * @return void
 */
function deleteEmailList($gapps, $html, $emailList)
{
    if ($html) {echo "<h2>Delete Email List</h2>\n";}
    
    $gapps->deleteEmailList($emailList);
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Add a recipient to an existing email list.
 * 
 * @param  Zend_Gdata_Gapps $gapps            The service object to use for communicating with the
 *                                            Google Apps server.
 * @param  boolean          $html             True if output should be formatted for display in a
 *                                            web browser.
 * @param  string           $recipientAddress The address of the recipient who should be added.
 * @param  string           $emailList        The name of the email address the recipient be added to.
 * @return void
 */
function addRecipientToEmailList($gapps, $html, $recipientAddress, 
        $emailList)
{
    if ($html) {echo "<h2>Subscribe Recipient</h2>\n";}
    
    $gapps->addRecipientToEmailList($recipientAddress, $emailList);
    
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Outputs the list of all recipients for a given email list.
 * 
 * @param  Zend_Gdata_Gapps $gapps     The service object to use for communicating with the Google
 *                                     Apps server.
 * @param  boolean          $html      True if output should be formatted for display in a web browser.
 * @param  string           $emailList The email list whose recipients should be output.
 * @return void
 */
function retrieveAllRecipients($gapps, $html, $emailList)
{
    if ($html) {echo "<h2>Email List Recipients For {$emailList}</h2>\n";}
    
    $feed = $gapps->retrieveAllRecipients($emailList);
    
    if ($html) {echo "<ul>\n";}
    
    foreach ($feed as $recipient) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $recipient->who->email;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Remove an existing recipient from an email list.
 * 
 * @param  Zend_Gdata_Gapps $gapps            The service object to use for communicating with the
 *                                            Google Apps server.
 * @param  boolean          $html             True if output should be formatted for display in a
 *                                            web browser.
 * @param  string           $recipientAddress The address of the recipient who should be removed.
 * @param  string           $emailList        The email list from which the recipient should be removed.
 * @return void
 */
function removeRecipientFromEmailList($gapps, $html, $recipientAddress,
        $emailList)
{
    if ($html) {echo "<h2>Unsubscribe Recipient</h2>\n";}
    
    $gapps->removeRecipientFromEmailList($recipientAddress, $emailList);
    
    if ($html) {echo "<p>Done.</p>\n";}
    
}
