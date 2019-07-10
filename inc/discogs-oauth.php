<?php
// This example uses the OAuthSimple library for PHP
// found here:  https://github.com/jrconlin/oauthsimple/tree/master/php
//
// For more information about the OAuth process for applications
// accessing Discogs API, read:
// http://www.discogs.com/developers
require 'oauth.php';
$oauthObject = new OAuthSimple();
$scope = 'http://api.discogs.com';

// Initialize the output in case we get stuck in the first step.
$output = 'Authorizing...';

#################
# CONFIGURATION #
#################

$config = array(
    'url' => 'http://handstandrecords.com',
    'user_agent' => 'HandstandRecords',
    'version' => '0.1'
);

// Fill in your API key/consumer key you received when you registered your 
// application with Discogs.
$signatures = array(
    'consumer_key' => 'NIOhHbkhyRWMUlxQfcpF',
    'shared_secret' => 'iSVvKZwhhfqMJTWPCnPBjyPZiEcuDwlI'
);

// Check if verifier exists.  If not, get a request token
if (!isset($_GET['oauth_verifier'])) {
    // To get a Request Token, we make a request to the OAuthGetRequestToken endpoint,
    // submitting the scope of the access we need (api.discogs.com)
	  // and also tell Discogs where to redirect once authorization is submitted
    $result = $oauthObject->sign( array(
        'path'      =>'http://api.discogs.com/oauth/request_token',
        'parameters'=> array(
            'scope'         => $scope,
            'oauth_callback'=> $config['url']
        ),
        'signatures'=> $signatures
    ));

    // The above object generates a simple URL that includes a signature, the 
    // needed parameters, and the web page that will handle our request.
    // Using the cUrl libary, we send a GET request to the signed URL
	  // then add the response into a string variable ($r)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, $config['user_agent'] . '/' . $config['version'] . ' +' . $config['url'] );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
    
    $r = curl_exec($ch);
    curl_close($ch);

    // Then we parse the string for the request token and the matching token secret. 
    parse_str($r, $returned_items);
    $request_token = $returned_items['oauth_token'];
    $request_token_secret = $returned_items['oauth_token_secret'];
	
    // We store the token and secret in a cookie for later when authorization is complete
    setcookie("oauth_token_secret", $request_token_secret, time()+3600);
    
    // Next we generate a URL for an authorization request, then redirect to that URL
    // so the user can authorize our request.  
    // The user could deny the request, so we should add some code later to handle that situation
    $result = $oauthObject->sign(array(
        'path'      =>'http://www.discogs.com/oauth/authorize',
        'parameters'=> array(
            'oauth_token' => $request_token
        ),
        'signatures'=> $signatures
    ));

    // Here is where we redirect
    header("Location:$result[signed_url]");
    exit;
}
else {
    // If we have a oauth_verifier, fetch the cookie and amend our signature array with the request
    // token and secret.
    $signatures['oauth_secret'] = $_COOKIE['oauth_token_secret'];
    $signatures['oauth_token'] = $_GET['oauth_token'];
    
    // Build the request-URL
    $result = $oauthObject->sign(array(
        'path'      => 'http://api.discogs.com/oauth/access_token',
        'parameters'=> array(
            'oauth_verifier' => $_GET['oauth_verifier'],
            'oauth_token'    => $_GET['oauth_token']),
        'signatures'=> $signatures));

    // ... and get the web page and store it as a string again.
    $ch = curl_init();
	  //Set the User-Agent Identifier
    curl_setopt($ch, CURLOPT_USERAGENT, $config['user_agent'] . '/' . $config['version'] . ' +' . $config['url'] );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
    $r = curl_exec($ch);

    // parse the string to get you access token
    parse_str($r, $returned_items);        
    $access_token = $returned_items['oauth_token'];
    $access_token_secret = $returned_items['oauth_token_secret'];
    
    // We can use this long-term access token to request Discogs API data,
    // for example, the identity of the authenticated user. 
    // All Discogs API data requests will have to be signed just as before,
    // but we can now bypass the authorization process and use the long-term
    // access token you hopefully store somewhere permanently.
    $oauth_props = array( 'oauth_token'     => $access_token,
                     'oauth_secret'    => $access_token_secret);

    // reset the oauth object
    $oauthObject->reset();
  	// rebuild it with the URL of the resource you want to access and the token/secret
    $result = $oauthObject->sign(array(
        'path'      => "$scope/oauth/identity",
        'signatures'=> $oauth_props)
	);

    // Now that we have our signed URL, we can make one more call to the API
    // which will grant us access to an authenticated resource
    // such as http://api.discogs.com/oauth/identity

    $url = $result['signed_url'];
	
    curl_setopt($ch, CURLOPT_USERAGENT, $config['user_agent'] . '/' . $config['version'] . ' +' . $config['url'] );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //Execute the curl session
    $output = curl_exec($ch);
	
    curl_close($ch);
	
    // print the JSON output to the page
    echo $output;
}        
?>