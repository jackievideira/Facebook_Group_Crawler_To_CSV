<?php
require_once 'Facebook/autoload.php';
session_start();
$fb = new Facebook\Facebook([
    'app_id' => '326587264865204', // Replace {app-id} with your app id
    'app_secret' => 'e245db43b0faf170d586857ee2a44427',
    'default_graph_version' => 'v3.2',
]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email', 'groups_access_member_info', 'publish_to_groups']; // Optional permissions
$loginUrl = $helper->getLoginUrl('fb-callback.php', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';