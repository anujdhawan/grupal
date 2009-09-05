<?php
/* 
 * grupal - Google Apps integrations for Drupal
 * Copyright © 2009 Sam Johnston <samj@samj.net> http://samj.net/
 *                  Australian Online Solutions Pty Ltd http://www.aos.net.au/
 * 
 * $Id: grupal.module 45 2009-03-09 19:00:29Z samj@samj.net $
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
require_once 'xmlseclibs/xmlseclibs.php';

/**
 * Creates a signed SAML response
 * @param string $userName user that has been authenticated
 * @param string $domain domain name
 * @param string $certificate certificate to be used for the signature (PEM encoded)
 * @param string $privateKey private key to be used for the signature (PEM encoded)
 * @param boolean $send sends response to output if true
 * @param boolean $debug enables debugging - e.g. SAML response can be inspected in source
 * @return string Signed SAML response
 */
function samlResponse($userName, $domain, $certificate, $privateKey, $send = false, $debug = false) {
	// sanity checks
	assert($userName);
	assert($domain);
	assert(strpos($certificate, '-----BEGIN CERTIFICATE-----') !== false);

	// identify key
	$dsaOrRsa = '';
	if (strpos($privateKey, '-----BEGIN RSA PRIVATE KEY-----') !== false) $dsaOrRsa = 'rsa';
//	if (strpos($privateKey, '-----BEGIN DSA PRIVATE KEY-----') !== false) $dsaOrRsa = 'dsa';
	assert($dsaOrRsa);
	
	// get request (and opaque RelayState token)
	if ($_GET['SAMLRequest']) {
		$relayState = $_GET['RelayState'];
		$requestXml = samlDecode($_GET['SAMLRequest']);
	} else if ($_POST['SAMLRequest']) {
		$relayState = $_POST['RelayState'];
		$requestXml = samlDecode($_POST['SAMLRequest']);
	} else {
		return false;
	}
	
	// parse request
	assert('$requestXml');
	if (class_exists("SimpleXMLElement")) { // PHP5
		$xml = new SimpleXMLElement($requestXml);
		$requestAttr['acsURL'] = $xml['AssertionConsumerServiceURL'];
		$requestAttr['requestID'] = $xml['ID'];
	} else { // expat
		$p = xml_parser_create();    
		$result = xml_parse_into_struct($p, $requestXml, $vals, $index);
		$requestAttr['acsURL'] = $vals[0]['attributes']['ASSERTIONCONSUMERSERVICEURL'];
		$requestAttr['requestID'] = $vals[0]['attributes']['ID'];
	}	
	assert('is_array($requestAttr)');

	// generate response
	$responseXml = samlGenerateResponse($userName, $domain,
		$requestAttr['requestID'], $requestAttr['acsURL'],
		$dsaOrRsa);
	assert('$responseXml');

	// sign response
	$signedResponseXml = samlSignResponse($responseXml, $certificate, $privateKey);

	// generate html form
	$html = <<<END_OF_FORM
<form name="samlForm" action="$requestAttr[acsURL]" method="post">
	<textarea style="display:none;" name="SAMLResponse">$signedResponseXml</textarea>
	<textarea style="display:none;" name="RelayState">$relayState</textarea>
	<input type="submit" />
</form>
END_OF_FORM;

	// add autosubmit (unless debugging)
	if (!$debug) $html .= '<script type="text/javascript">document.samlForm.submit();</script>';
	
	// send or return html form
	if ($send) {
		print $html;
	} else {
		return $html;
	}
}

/**
 * Inserts a signature into the Assertion element, just before the Subject element
 * @param string $responseXml Assertion to be signed as an XML string
 * @param string $certificate certificate to be used for the signature (PEM encoded)
 * @param string $privateKey private key to be used for the signature (PEM encoded)
 * @param string $pkPassphrase private key passphrase
 * @return string Signed SAML response
 */
function samlSignResponse($responseXml, $certificate, $privateKey, $pkPassphrase = null) {
	$doc = new DOMDocument();
	$doc->loadXML($responseXml);

	// find Assertion and Subject nodes
	$assertion = $doc->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:assertion', 'Assertion')->item(0);
	//print_r($assertion->getAttribute('Id'));
	$subject = $assertion->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:assertion', 'Subject')->item(0);
    //print_r($subject->childNodes->item(1)->getAttribute('Format'));

	$xmlSecDSig = new XMLSecurityDSig();
	$xmlSecDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
	$xmlSecDSig->addReference($assertion, XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::EXC_C14N), array('id_name' => 'ID'));

	// identify key
	$xmlSecKeyType = '';
	if (strpos($privateKey, '-----BEGIN RSA PRIVATE KEY-----') !== false) $xmlSecKeyType = XMLSecurityKey::RSA_SHA1;
//	if (strpos($privateKey, '-----BEGIN DSA PRIVATE KEY-----') !== false) $xmlSecKeyType = XMLSecurityKey::DSA_SHA1;
	assert('$xmlSecKeyType');

	$xmlSecKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
	$xmlSecKey->loadKey($privateKey);
 	if ($pkPassphrase) $xmlSecKey->passphrase = $pkPassphrase;

	// sign XML
	$xmlSecDSig->sign($xmlSecKey);
	
	// embed certificate
	$xmlSecDSig->add509Cert($certificate);

	// insert signature before subject node
	$xmlSecDSig->insertSignature($assertion, $subject);
	
	$doc->save('/tmp/signed.xml');
	
	$samlResponse = $doc->saveXML();

	return $samlResponse;
}

/**
 * Creates a complete SAML response from a template
 * @param string $userName The Google Apps username
 * @param string $dsaOrRsa 'dsa' or 'rsa'
 * @param string $requestID SAML request ID
 * @param string $destinationUrl ACS URL that the response is submitted to
 * @param string $notBefore ISO 8601 formatted valid from date
 * @param string $notOnOrAfter ISO 8601 formatted expiry date
 * @return string SAML response.
 */
function samlGenerateResponse($userName, $issuerDomain, $requestId, $destinationUrl,
	$dsaOrRsa = 'dsa', $notBefore = null, $notOnOrAfter = null) {
	assert('in_array(strtolower($dsaOrRsa), array("dsa", "rsa"))');

	$saml = array(
		'ISSUER'          => ((($_SERVER['HTTPS'] == 'on') ? "https://" : "http://") .
		  $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF']),
		'USERNAME'        => $userName,
//		'ISSUER_DOMAIN'   => $issuerDomain,
		'REQUEST_ID'      => $requestId,
		'DESTINATION'     => $destinationUrl,
		'DSAORRSA'        => strtolower($dsaOrRsa),
//		'ASSERTION_ID'    => samlCreateId(),
		'RESPONSE_ID'     => samlCreateId(),
		'AUTHN_INSTANT'   => samlDateTime(time()),
		'ISSUE_INSTANT'   => samlDateTime(time()),
		'NOT_ON_OR_AFTER' => $notOnOrAfter ? $notOnOrAfter : samlDateTime(strtotime('+10 minutes')),
		'NOT_BEFORE'      => $notBefore ? $notBefore : samlDateTime(strtotime('-5 minutes')),
	);

  $response = <<< END_OF_SAML
<samlp:Response ID="$saml[RESPONSE_ID]" IssueInstant="$saml[ISSUE_INSTANT]" Version="2.0" Destination="$saml[DESTINATION]" InResponseTo="$saml[REQUEST_ID]"
        xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
        xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
        xmlns:xs="http://www.w3.org/2001/XMLSchema"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">$saml[ISSUER]</saml:Issuer>
        <samlp:Status>
                <samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/>
        </samlp:Status>
        <saml:Assertion IssueInstant="$saml[ISSUE_INSTANT]" Version="2.0"
                xmlns="urn:oasis:names:tc:SAML:2.0:assertion">
                <saml:Issuer>$saml[ISSUER]</saml:Issuer>
                <saml:Subject>
                        <saml:NameID Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress" SPNameQualifier="google.com">$saml[USERNAME]</saml:NameID>
                        <saml:SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
                                <saml:SubjectConfirmationData
                                        Recipient="$saml[DESTINATION]"
                                        NotOnOrAfter="$saml[NOT_ON_OR_AFTER]"
                                        InResponseTo="$saml[REQUEST_ID]"/>
                        </saml:SubjectConfirmation>
                </saml:Subject>
                <saml:Conditions NotBefore="$saml[NOT_BEFORE]"
                        NotOnOrAfter="$saml[NOT_ON_OR_AFTER]">
                        <saml:AudienceRestriction>
                                <saml:Audience>google.com</saml:Audience>
                        </saml:AudienceRestriction>
                </saml:Conditions>
                <saml:AuthnStatement AuthnInstant="$saml[AUTHN_INSTANT]">
                        <saml:AuthnContext>
                                <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Password</saml:AuthnContextClassRef>
                        </saml:AuthnContext>
                </saml:AuthnStatement>
        </saml:Assertion>
</samlp:Response>

END_OF_SAML;

  return $response;
}

/**
 * Generates random IDs per SAML spec which requires 128..160 bits (40x4=160)
 * @return string
 */
function samlCreateId() {
	$rndChars = 'abcdefghijklmnop';
	$rndId = '';

	for ($i = 0; $i < 40; $i++ ) {
		$rndId .= $rndChars[rand(0,strlen($rndChars)-1)];
	}

	return $rndId;
}

/**
 * Converts unix timestamp into xsd:dateTime format
 * @param timestamp int unix_t to convert to xsd:dateTime ISO 8601
 * @return string
 */
function samlDateTime($timestamp) {
	return gmdate('Y-m-d\TH:i:s\Z', $timestamp);
}

/**
 * Encodes SAML message
 * @param string $msg
 * @return string
 */
function samlEncode($msg) {
	return urlencode(base64_encode(gzdeflate($msg)));
}

/**
 * Decodes SAML message
 * @param string $msg
 * @return string
 */
function samlDecode($msg) {
	$x = gzinflate(base64_decode($msg));
	return $x ? $x : gzuncompress(base64_decode($msg));
}

?>