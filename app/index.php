<?php

require '../vendor/autoload.php';


$token = "GET YOUR OWN TOKEN";

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);



$client = new \Github\Client();
$client->authenticate($token, null, \Github\Client::AUTH_URL_TOKEN);


$openPullRequests = $client->api('pull_request')->all('mmz-srf', 'ezpublish-community');

$openRequests = array();
$reviewedRequest = array(); 
foreach ($openPullRequests as $request) {
        $comments = $client->api('issues')->comments()->all('mmz-srf', 'srf-cms', $request['number']);
        $isReviewed = false;
        foreach($comments as $comment) {

            if (stripos($comment['body'], 'lgtm') !== false) {
                $isReviewed = true;
                $reviewedRequest[] = $request;
                break;
            }
        }
        if (!$isReviewed) {
            $openRequests[] = $request;
        }    
    
}

echo $twig->render('index.html', array(
    'open' => $openRequests,
    'closed' => $reviewedRequest
));
