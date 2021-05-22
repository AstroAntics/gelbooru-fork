<?php
        $user = new user();
        
        //No access for guests or non-moderators
        if (!$user->check_log()) || (!$user->gotpermission('delete_forum_posts')))
        {
                echo "Sorry, this is a moderator-only function.";
                exit;
        }

        if ((isset($_POST['thread-id'])) && isset($_POST['nuker'])))
        {
                //If we have a reason, we'll need to do a little more before the deletion.
                if (isset($_POST['reason']))
                {
                        $thread_id = $db->real_escape_string($_POST['thread-id']);
                        $nuker = $db->real_escape_string($_POST['username']);
                        
                        //We want to make a record of the nuked thread first.
                        $query = "INSERT INTO $nuked_thread_table (id, thread-id, date-nuked, nuked-by, reason) 
                        VALUES '', '$thread_id', 'NOW()', '$nuker', '$reason'";
                        $db->query($query) or die(db->error());
                        
                        //Now delete the actual thread. First posts don't matter because we'll delete the entire topic.
                        $query = "DELETE FROM $forum_post_table WHERE id='thread_id'";
                        $db->query($query) or die($db->error());
                                
                }
                
                else
                {
                        $thread_id = $db->real_escape_string($_POST['thread-id']);
                        $nuker = $db->real_escape_string($_POST['username']);
                        
                        //Once again, record the nuked thread.
                        $query = "INSERT INTO $nuked_thread_table (id, thread-id, date-nuked, nuked-by, reason) 
                        VALUES '', '$thread_id', 'NOW()', '$nuker', ''";
                        $db->query($query) or die($db->error());
                        
                        //Now delete the thread.
                        $query = "DELETE FROM $forum_post_table WHERE id='thread-id'";
                        $db->query($query) or die($db->error());                        
                }
        }
        
        //Invalid input
        else
        {
                echo "You must specify a thread ID and/or target username.";
                exit;
        }

?>

<form action="rmthread.php" method="post">
        <input type="hidden" name="thread-id" value="<?php echo . $thread_id . ?">; >"
        <input type="hidden" name="nuker" value= "<?php echo . $username . ?">; >"
        <div style="text-align: center;">
                <p> Are you sure you want to nuke this thread? <br> <strong>This cannot be undone.</strong> </p>
                <hr>
                <p><strong>Thread title:</strong> . <?php echo . $thread_title . ?>; 
                <input type="text" name="nuke-reason" value="nuke-reason" placeholder="Reason goes here... (optional)">
                <label for="nuke-reason">You may specify a reason here. <br> <strong>All reasons will be publicly visible.</strong> 
                Don\'t post anything that you\'d regret later.</label>
                <input type="submit" value="Nuke thread" name="nuke-thread" value="Nuke it">
        </div>  
</form>
