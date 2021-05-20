<?php
//viewpm.php?id=#####

$int_id = intval($_GET['id']);
$user = new user();

if (!$user->check_log())
{
        die("The PM system is available to registered users only.");
}

else
{
        $username = $checked_username;
}
        
//No message ID... don't show anything. Technically wrong but it works for the user.
if (!isset($int_id))
{
        die("Sorry, this message is private.");
}

else
{
        //Get message details based on ID.
        $query = "SELECT FROM pm_table WHERE id='$int_id'";
        $sender; // = get_pm_sender($int_id);
        $recipient; // = get_pm_recipient($int_id);
        
        $is_admin = $user->is_admin();
        
        
        if (!$user->is_admin()
        {
                //No access if sender/recipient is not the intended target
                if ($sender !== $username || $recipient !== $username)
                {
                        die("This message is private.");
                }
        }
            
        else
        {
                //Simply echo out the message for now
                echo . $message['sender'] . "sent you the following:" . $message['text'] . "on" . $message['datesent']";
        }
        
        
            
            
        
}


//Boilerplate file for viewing personal messages.
