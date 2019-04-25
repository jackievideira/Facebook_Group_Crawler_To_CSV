<?php
require_once 'Facebook/autoload.php';


if(isset($_GET['user_login'])) {
    $fb = new \Facebook\Facebook([
        'app_id' => '326587264865204',
        'app_secret' => 'e245db43b0faf170d586857ee2a44427',
        'default_graph_version' => 'v3.2',
        'default_access_token' => $_GET['user_login']
    ]);
    
} else {
        $fb = new \Facebook\Facebook([
        'app_id' => '326587264865204',
        'app_secret' => 'e245db43b0faf170d586857ee2a44427',
        'default_graph_version' => 'v3.2',
        'default_access_token' => 'EAAEpB4XpE7QBAIkVuZAioWH6o1Lgrcm94oNY6mik63lu4aFamsbEVj3WvaZANT3N5Gb7sZCJLAVSvVZB37ZAMx2KDqeF34VuEXZA711NLPhh6qxP5EruVIHjIfq0toQJzWOosXtYRDLss85AUKlELTMp61MuN7xfew9ZCvOGPxMD2iOmwrAZCRAqEWxP62l2EI7LhL36777XUQZDZD', // optional
    
    // TEMPORARY ACCESS CODE
     
        ]);
    }



$response = $fb->get(
    '/me/groups?fields=id,name,administrator',
    //'default_access_token'
    );
$groups = $response->getGraphEdge();


print "<h2>Choose the group you'd like to pull information from</h2><ul style='list-style: none;'>";
do {
        foreach ($groups as $group) { // for each graphedge as graphnode
        // var_dump($group->asArray());
        
        $group_admin = $group['administrator'];
        $group_name = $group['name'];
        $group_id = $group['id'];
        if($group_admin == true){
    
            printf("<li><a href='getData.php?groupid=$group_id&groupname=$group_name'>$group_name</a></li>\n");
    
        }
    printf("</ul>");
    }
} while($groups = $fb->next($groups));