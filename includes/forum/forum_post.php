<?php
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$limit = 15;
		$id = $db->real_escape_string($_GET['id']);
		$query = "SELECT topic_id FROM $forum_post_table WHERE id='$id'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_assoc();
		$fid = $row['topic_id'];
		$result->free_result();
		if(!is_numeric($fid))
		{
			header("Location:index.php?page=forum&s=list");
			exit;
		}
		$postcount = 0;
		$query = "SELECT id FROM $forum_post_table WHERE topic_id='$fid'";
		$result = $db->query($query) or die($db->error);
		
		while($row = $result->fetch_assoc())
		{
			$postcount++;
			
			if($row['id'] == $id) 
			{
				break;
			}
		}
		
		$result->free_result();
		$threadcount = 1;
		while(($threadcount*$limit)<$postcount)
		{
			$threadcount++;
		}
		$pid = $limit*($threadcount - 1);
		header("Location:index.php?page=forum&s=view&id=$fid&pid=$pid#$id");
	}
?>
