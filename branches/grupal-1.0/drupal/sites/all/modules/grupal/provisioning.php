<?php
// $Id$

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
