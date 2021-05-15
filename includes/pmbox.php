<?php

//Message limit per page
$msg_per_page = 20;

//Total number of pages to display
$page_limit = 6;

$user = new user();
$misc = new misc();

if ($user->check_log())
{
        $username = $checked_username;
}

//Most recent messages should come first.
$query = "SELECT COUNT(*) FROM $pm_table WHERE sender = '$username' AND recipient='$username' AND status != 'deleted' ORDER BY date DESC";
$result = $db->query($query);
$msg = $result->fetch_assoc();
$num_msg = $msg['COUNT(*)'];
$result->free_result();

if ($num_msg === 0)
{
        echo '<span class="block center boldish"> You have no private messages. </span>';
}

//Show the PM options to the user.
echo '<div class="btn-medium force-left no-shadow">Compose PM</div>';
echo '<div class="btn-medium force-right no-shadow">Find PM blocks</div>';

echo '<div id="message_holder"';

while ($msg = $result->fetch_assoc())
{
        $mdate = $misc->date_words($msg['msg_date']); //Fetch user-friendly message date.
        
        //No pagination necessary here.
        echo "<table>";
                echo "<th>Sender</th>";
                echo "<th>Subject</th>";
                echo "<th>Date</th>";
                echo "<th>Delete Message?</th>";
        
                echo "<tr>";
                        echo "<td>" . $msg['sender'] . "</td>";
                        echo "<td>" . $msg['subject'] . "</td>";
                        echo "<td>" . $mdate . "</td>";
                        echo "<td>" . '<i class="fa fa-times danger' 'title="Delete" . '$msg['sender']' . "\'s post?"'>' ."</td>";
                echo "</tr>";
                        
                        echo "<a href='"nukepm.php?pm_id=all'> Clear All </a>";
       
       echo "</table>";
}
