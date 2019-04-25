<?php
include 'chooseGroup.php';
require_once 'Facebook/autoload.php';

if(isset($_GET['groupid'])) {
    $this_group = $_GET['groupid'];
    $this_group_name = $_GET['groupname'];
    processData($fb, $this_group, $this_group_name);
}
// Should add an option to change since, until and limit



function processData($fb, $group_id, $groupname){
   // Add a condition for since and until into the link
   $fbdata = $fb->get(
        '/'.$group_id.'/feed?fields=message,id,likes{id,name,username},reactions{id,name,username,type},created_time,story,link,picture,status_type,comments{created_time,id,message,from,likes{id,username},reactions{id,type,username},comments{created_time,from,message,id,likes{id,username},reactions{type,id,username}}},from',
    );
    $postfeed = $fbdata->getGraphEdge();
    $chatfeed_CSV = array();
    $chatcomments_CSV = array();
    $chatlikes_CSV = array();
    $chatreactions_CSV = array();
    $chatfeed_CSV[0] = array('Id', 'UserId', 'UserName', 'CreatedTime', 'StatusType', 'Message', 'Story', 'Link', 'Picture');
    $chatcomments_CSV[0] = array('PostId', 'Id', 'UserId', 'UserName', 'CreatedTime', 'Message');
    $chatlikes_CSV[0] = array('ObjectId', 'UserId', 'UserName');
    $chatreactions_CSV[0] = array('ObjectId', 'UserId', 'UserName', 'Type');
    $chatcount = 1;
    $comcount = 1;
    $likecount = 1;
    $reactcount = 1;
    do {
        foreach ($postfeed as $post )
        {   
            $post_id = $post['id'];
            $post_userid = $post['from']['id'];
            $post_username = $post['from']['name'];
            $post_created = $post['created_time'];
            $post_created = $post_created->format('d-m-Y H:i:s');
            $post_statustype = $post['status_type'];
            $post_message = $post['message'];
            $post_story = $post['story'];
            $post_link = $post['link'];
            $post_pic = $post['picture'];
            
            $chatfeed_CSV[$chatcount] = array($post_id, $post_userid, $post_username, $post_created, $post_statustype, $post_message, $post_story, $post_link, $post_pic);
            $chatcount = $chatcount + 1;
            if ($post['comments']){
                $commentfeed = $post['comments'];
                
                foreach($commentfeed as $comment){
                    $comment_id = $comment['id'];
                    $comment_userid = $comment['from']['id'];
                    $comment_username = $comment['from']['name'];
                    $comment_created = $comment['created_time'];
                    $comment_created = $comment_created->format('d-m-Y H:i:s');
                    $comment_message = $comment['message'];
                    
                    $chatcomments_CSV[$comcount] = array($post_id, $comment_id, $comment_userid, $comment_username, $comment_created, $comment_message);
                    $comcount = $comcount + 1;
                    
                    if ($comment['comments']){
                        $comment2feed = $comment['comments'];
                        
                        foreach($comment2feed as $comment){
                            $comment_id = $comment['id'];
                            $comment_userid = $comment['from']['id'];
                            $comment_username = $comment['from']['name'];
                            $comment_created = $comment['created_time'];
                            $comment_created = $comment_created->format('d-m-Y H:i:s');
                            $comment_message = $comment['message'];
                            
                            $chatcomments_CSV[$comcount] = array($post_id, $comment_id, $comment_userid, $comment_username, $comment_created, $comment_message);
                            $comcount = $comcount + 1;
                            
                            if ($comment['likes']){
                                $commentlikefeed = $comment['likes'];
                                foreach($commentlikefeed as $like){
                                    $like_userid = $like['id'];
                                    $like_username = $like['name'];
                                    
                                    $chatlikes_CSV[$likecount] = array($comment_id, $like_userid, $like_username);
                                    $likecount = $likecount + 1;
                                }
                                
                            }
                            if ($comment['reactions']){
                                $commentreactionfeed = $comment['reactions'];
                                foreach($commentreactionfeed as $reaction){
                                    $reaction_userid = $reaction['id'];
                                    $reaction_username = $reaction['name'];
                                    $reaction_type = $reaction['type'];
                                    
                                    $chatreactions_CSV[$reactcount] = array($comment_id, $reaction_userid, $reaction_username, $reaction_type);
                                    $reactcount = $reactcount + 1;
                                }
                            }
                        }
                    }
                    if ($comment['likes']){
                        $commentlikefeed = $comment['likes'];
                        foreach($commentlikefeed as $like){
                            $like_userid = $like['id'];
                            $like_username = $like['name'];
                            
                            $chatlikes_CSV[$likecount] = array($comment_id, $like_userid, $like_username);
                            $likecount = $likecount + 1;
                        }
                        
                    }
                    if ($comment['reactions']){
                        $commentreactionfeed = $comment['reactions'];
                        foreach($commentreactionfeed as $reaction){
                            $reaction_userid = $reaction['id'];
                            $reaction_username = $reaction['name'];
                            $reaction_type = $reaction['type'];
                            
                            $chatreactions_CSV[$reactcount] = array($comment_id, $reaction_userid, $reaction_username, $reaction_type);
                            $reactcount = $reactcount + 1;
                        }
                    }
                }
            }
            if ($post['likes']){
                $likefeed = $post['likes'];
                foreach($likefeed as $like){
                    $like_userid = $like['id'];
                    $like_username = $like['name'];
                    
                    $chatlikes_CSV[$likecount] = array($comment_id, $like_userid, $like_username);
                    $likecount = $likecount + 1;
                }
                
            }
            if ($post['reactions']){
                $reactionfeed = $post['reactions'];
                foreach($reactionfeed as $reaction){
                    $reaction_userid = $reaction['id'];
                    $reaction_username = $reaction['name'];
                    $reaction_type = $reaction['type'];
                    
                    $chatreactions_CSV[$reactcount] = array($comment_id, $reaction_userid, $reaction_username, $reaction_type);
                    $reactcount = $reactcount + 1;
                }
            }
        }
    } while($postfeed = $fb->next($postfeed));
    

    writeCSV($chatfeed_CSV, $chatcomments_CSV, $chatlikes_CSV, $chatreactions_CSV, $groupname); 
} 

function writeCSV($feed, $comments, $likes, $reactions, $this_group_name){
    ob_clean();
    
    $zip = new ZipArchive();
    $zipname = ".$this_group_name._chat.zip";
    $zip->open($zipname, ZipArchive::CREATE);
    
    $chatfeedfilename = ".$this_group_name._chat_feed.csv";
    $fp = fopen('php://temp', 'w');
    
    foreach($feed as $line){
        fputcsv($fp, $line);
    }
    
    rewind($fp);
    
    $zip->addFromString($chatfeedfilename,stream_get_contents($fp));
    
    fclose($fp);
    
    $commentfeedfilename = ".$this_group_name._chat_comments.csv";
    $fp = fopen('php://temp', 'w');
    
    foreach($comments as $line){
        fputcsv($fp, $line);
    }
    
    rewind($fp);
    
    $zip->addFromString($commentfeedfilename, stream_get_contents($fp));
    
    fclose($fp);
    
    $likefeedfilename = ".$this_group_name._chat_likes.csv";
    $fp = fopen('php://temp', 'w');
    foreach($likes as $line){
        fputcsv($fp, $line);
    }
    
    rewind($fp);
    
    $zip->addFromString($likefeedfilename, stream_get_contents($fp));
    
    fclose($fp);
    
    $reactionfeedfilename = ".$this_group_name._chat_reactions.csv";
    $fp = fopen('php://temp', 'w');
    foreach($reactions as $line){
        fputcsv($fp, $line);
    }
    
    rewind($fp);
    
    $zip->addFromString($reactionfeedfilename, stream_get_contents($fp));
    
    fclose($fp);
    
    $zip->close();
    
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename='.$zipname);
    header('Content-Length: ' . filesize($zipname));
    readfile($zipname);
    
    unlink($zipname);
    
    exit();
}

?> 