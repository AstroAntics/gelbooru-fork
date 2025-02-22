<?php
	//number of topics/page
	$limit = 20;
	//number of pages to display. number - 1. ex: for 5 value should be 4
	$page_limit = 6;
	$user = new user();
	$misc = new misc();
	header("Cache-Control: store, cache");
	header("Pragma: cache");

	/* We'll take the page directly from the database here if the page ID is set (of course, we'll need to run it through the database first. 
	Otherwise, assume the page ID is 0. */
	(isset($_GET['pid']) && $_GET['pid'] != "" && is_numeric($_GET['pid']) && $_GET['pid'] >= 0) ? $page = $db->real_escape_string($_GET['pid']) : $page = 0;

	$id = $db->real_escape_string($_GET['id']);
	if($user->check_log())
	{
		$uname = $checked_username;
		$uid = checked_user_id;
	}

	/* The nuked thread check goes before everything else. 
	We try to select a row from the nuked threads table where the topic ids match. */
	$query = "SELECT FROM $nuked_thread_table WHERE topic_id='$id'"; 
	$result = $db->query($query);
	$row = $result->fetch_assoc();
	$result->free_result();
	
	if ($row['topic_id'] === $id)
	{
		header("Location: /nuked-thread.php?id= . $row['topic-id'] . ");
	}
	
	
	$query = "SELECT COUNT(*) FROM $forum_post_table WHERE topic_id='$id'";
	$result = $db->query($query);
	$row = $result->fetch_assoc();
	$numrows = $row['COUNT(*)'];
	$result->free_result();

	//It's friendlier if we let the user know about the nonexistent discussion instead of yeeting them back to the forum homepage.
	if($numrows == 0)
	{
		echo "Sorry, no discussion with that id exists.";
		exit;
	}
	require "includes/header.php";
	$query = "SELECT t1.id, t1.title, t1.post, t1.author, t1.creation_date, t2.creation_post 
	FROM $forum_post_table  AS t1 JOIN $forum_topic_table AS t2 ON t2.id=t1.topic_id 
	WHERE t1.topic_id='$id' ORDER BY id LIMIT $page, $limit";
	
	$result = $db->query($query) or die(mysql_error());

	//Forum page starts here
	print'<div id="forum" class="response-list">';

	/* TODO: This whole thing needs a good cleanup. 
	Especially the messy statements re: authorship and post editing. 
	(Refactoring this makes me feel like washing my hands.) */
	while($row = $result->fetch_assoc())
	{
		$date_made = $misc->date_words($row['creation_date']); //Render user-friendly date field
		print '<div class="post">
			<div class="author">
				<h6 class="author">
					<a name="'.$row['id'].'"></a>
					<a href="index.php?page=account_profile&amp;uname='.$row['author'].'" style="font-size: 14px;">'.$row['author'].'</a>
				</h6>
				<span class="date">'.$date_made.' </span>
			</div>
			<article>
				<h6 class="response-title">'.$row['title'].'</h6>
				<div class="body">'.$misc->short_url($misc->swap_bbs_tags($misc->linebreaks($row['post']))).'</div>			
			<div class="footer">';
		
		//HACK: Show "edited by" text based on edit count (obviously shouldn't occur when edit count is 0)
		if (count($row['edit_count']) >= 1) 
		{ 
			echo '<span class="edit-message">'
				"edited by" . $row['edited_by'] . "on" . $row['edit_time'];
			echo '</span>';
		}
    	
		($uname == $row['author'] || $user->gotpermission('edit_forum_posts')) ? 
		echo '<a href="#" onclick="showHide(\'c'.$row['id'].'\'); return false;">edit</a> |' 
		: echo '<a href="">edit</a> |';
		
		echo ' <a href="#" onclick="javascript:document.getElementById(\'reply_box\').value=document.getElementById(\'reply_box\').value+\'[quote]'.$row['author'].' said:\r\n'.str_replace("'","\'",str_replace("\r\n",'\r\n',str_replace('&#039;',"'",$row['post']))).'[/quote]\'; return false;">quote</a> '; 
		
		//Do not let the user delete a post if it's the first post - that breaks the whole thread.
		if($user->gotpermission('delete_forum_posts') && $row['id'] != $row['creation_post']) 
		{
			print ' | <a href="index.php?page=forum&amp;s=remove&amp;pid='.$id.'&amp;cid='.$row['id'].'">remove</a><br />';
		}
		
		//Note: Testing a "hide post" function where the post is hidden but not actually deleted (mod only)
		/*
			if ($user->gotpermission('delete_forum_posts'))
			{
				print ' | <a href="index.php?page=forum&amp;s=hide&amp;pid='.$id.'& amp;cid='.$row['id'].'">hide this post</a>';
			}
		*/
		
		if($uname == $row['author'] || $user->gotpermission('edit_forum_posts')) 
		{
			print '<form method="post" action="index.php?page=forum&amp;s=edit&amp;pid='.$id.'&amp;cid='.$row['id'].'&amp;ppid='.$page.'" 
			style="display:none" id="c'.$row['id'].'">
				<table>
				<tr>
					<td>
						<input type="text" name="title" value="'.$row['title'].'"/>
					</td>
				</tr>
				<tr>
					<td>
						<textarea name="post" rows="4" cols="6" style="width: 450px; height: 150px;">'.$row['post'].'</textarea>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="submit" value="Edit"/>
					</td>
				</tr>
			</table>
			</form>';
		}
		
		echo '</div></article></div>';
	}
	
	echo '<div class="paginator"><div id="paginator">';
	$misc = new misc();
	print $misc->pagination($_GET['page'],$_GET['s'],$row['id'],$limit,$page_limit,$numrows,$_GET['pid'],$_GET['tags']);
	echo '</div><center><br /><br />';
	
	$query = "SELECT locked FROM $forum_topic_table WHERE id='$id' LIMIT 1";
	$result = $db->query($query) or die(mysql_error());
	$row = $result->fetch_assoc();
	
	print ($row['locked'] == false) ? '<a href="#" onclick="showHide(\'reply\'); return false;">Reply</a> | ' : '';
	print '<a href="index.php?page=forum&amp;s=add">New Topic</a> | 
	<a href="'.$site_url.'/help/">Help</a> | 
	<b><a href="'.$site_url.'/index.php?page=forum&amp;s=list">Forum Index</a></b>';

	//We only want to show the unlock/lock topic dialogue if the user has permission to lock forum topics.
	if ($user->gotpermission('lock_forum_topics'))
	{
		if ($row['locked'] == false)
		{
			print ' | <a href="index.php?page=forum&amp;s=edit&amp;lock=false&amp;id='.$id.'&amp;pid='.$page.'">Unlock topic</a>';
		}
		
		else
		{
			print ' | <a href="index.php?page=forum&amp;s=edit&amp;lock=true&amp;id='.$id.'&amp;pid='.$page.'">Lock topic</a>';
		}
	}		
	
	//Ugh, I hate inline comments, but I'm testing out a ban system which prohibits adding posts to threads if you're forum banned.
	if($row['locked'] == false /* && $user->is_forum_banned() === false*/)
	{
		echo '</center>
		<br>
		<br>
		<form method="post" action="index.php?page=forum&amp;s=add&amp;t=post&amp;pid='.$id.'" style="display:none" id="reply">
			<table>
				<tr>
					<td>Title
					<br>
					<input type="text" name="title" value=""/>
					</td>
				</tr>
				<tr>
					<td>Body
						<br>
						<textarea id="reply_box" name="post" rows="4" cols="6" style="padding-left: 5px; padding-right: 5px; width: 600px; height: 200px;">
						</textarea>
					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="l" value="'.$limit.'"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="conf" id="conf" value="0"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="submit" value="Post"/>
					</td>
				</tr>
			</table>
		</form>
		<script type="text/javascript">
		document.getElementById(\'conf\').value=1;
		</script>';
	}
?>
</div></div></body></html>
