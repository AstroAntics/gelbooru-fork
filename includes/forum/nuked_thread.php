<?php

$id = $db->real_escape_string($_GET['id']);

$query = "SELECT FROM nuked_thread_table WHERE topic_id = '$id'";
$result = $db->query($query);
$row = $result->fetch_assoc();
$result->free_result();

$has_reason = !(empty($row['reason']));

$reason = ($has_reason) ? $row['reason'] : "";

//We're viewing a nuked topic
if ($row['topic_id'] === $id)
{
        print '<div class="pull-center bigtext t-bold">'; 
        print '<p>This thread has been nuked.</p>';
              
        //HACK: Because blank reasons can't be submitted. Rough workaround.
        if ($reason)
        {
                print "<p> . $row['reason'] . </p>";
        }
        
        print '</div>';
}

//Ok, so the topic isn't nuked... just show the nuked screen
else
{
        print '<div class="pull-center bigtext t-bold">'; 
        print '<p>This thread has been nuked.</p>';
        print '</div>';
}
