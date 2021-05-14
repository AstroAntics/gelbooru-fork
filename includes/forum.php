<?php
	$s = $_GET['s'];

	switch ($s)
	{
		case "list":
		require "forum/forum_list.php";
		break;
		
		case "view":
		require "forum/forum_view.php";
		break;
			
		case "remove":
		require "forum/forum_remove.php";
		break;
			
		case "edit":
		require "forum/forum_edit.php";
		break;
			
		case "add":
		require "forum/forum_add.php";
		break;
			
		case "search":
		require "forum/forum_search.php";
		break;
			
			
		case "post":
		require "forum/forum_post.php";
		break;
			
		default:
		header("Location: index.php");
		break;
	}
?>
