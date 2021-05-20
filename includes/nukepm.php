<?php
//Boilerplate template for deleting PMs.

$id = $_POST['pm_id'];
$mode = $_POST['mode'];

//Deleting all PMs
if (isset($mode) && $mode === "all")
{
        $query = "UPDATE $pm_table SET deleted=1 WHERE recipient = '$recipient'";
        $result = $db->query($query) or die ('mysql_error())');
        if ($result) 
        {  
                echo "Your inbox has been cleared successfully. Returning to inbox..."; 
                header ("Location: /pmbox.php");
        } 
        
        else 
        { 
                die("Sorry, we couldn't clear your inbox. Please try again."); 
        }
}

//Deleting a single PM
if (isset($id))
{
        $query = "UPDATE $pm_table SET deleted = 1 WHERE pm_id = '$id'";
        $result = $db->query($query) or die('mysql_error())');
        if ($result)
        {
                echo "PM deleted. Returning to inbox...";
                header("Location: /pmbox.php");
        }
        else
        {
                echo "Sorry, we couldn't delete your PM.";
                header ("Location: /pmbox.php");
        }
}

else
{
        echo "We'll need an ID to do that.";
        header("Location: /pmbox.php");
}

