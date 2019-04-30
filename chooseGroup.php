<?php
require_once 'Facebook/autoload.php';

$extrastuff = '';

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
        'default_access_token' => 'EAAEpB4XpE7QBAIkrFpL63IC1CQ9XBd1MsMqwUNXul55iWUnKXtArhUCIOfNXpvEnXpGG6ZA0pjXyZAa87DZBU5a7ZBtDczBoPkimDxoOYGJhHkeAnjcom64MrfZBHs6SYl0D53JhM6FkE4SBQBB4GRHt13fxZAw3lthPfab9ZA92drJ7XxqlGZAQIiKfXxEpcZANMkOgdyXylxwZDZD', // optional
    
    // TEMPORARY ACCESS CODE
     
        ]);
    }
$ntl = '';
$since = '';
if(isset($_GET['ntl'])){
        $ntl = $_GET['ntl'];
        //$extrastuff = $extrastuff.'&until='.$ntl;
    }
if(isset($_GET['snc'])){
        $since = $_GET['snc'];
        //$extrastuff = $extrastuff.'&since='.$since;
    }


$response = $fb->get(
    '/me/groups?fields=id,name,administrator',
    //'default_access_token'
    );
$groups = $response->getGraphEdge();


print "<h2>Choose the group you'd like to pull information from</h2>";
// Date picker
printf("Enter two dates in format MM/DD/YYYY and click update to denote the date range<br>");
printf('<form action="chooseGroup.php" method="get">
Since: <input type="text" name="snc"> Until: <input type="text" name="ntl">
<input type="submit"></form><br>
');
printf("<ul style='list-style: none;'>");
do {
        foreach ($groups as $group) { // for each graphedge as graphnode
        // var_dump($group->asArray());
        
        $group_admin = $group['administrator'];
        $group_name = $group['name'];
        $group_id = $group['id'];
        if($group_admin == true){
    
            printf("<li><a href='getData.php?groupid=$group_id&groupname=$group_name&since=$since&until=$ntl'>$group_name</a></li>\n");
        }
    }

} while($groups = $fb->next($groups));
printf("</ul>");