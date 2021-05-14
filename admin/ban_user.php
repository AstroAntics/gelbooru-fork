<?php
	if(!defined('_IN_ADMIN_HEADER_'))
		die;
		
	set_time_limit(0);
	$user = new user();
	if(!$user->gotpermission('is_admin'))
	{
		print "No permission to access this.";
		exit;
	}	
	if(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
	{
		$ban_id = $db->real_escape_string($_GET['user_id']);
		$query = "SELECT id, user, ip FROM $user_table WHERE id = '$ban_id' LIMIT 1";
		$result = $db->query($query);
		$row = $result->fetch_assoc();		
		if(!isset($_POST['ban_reason']))
		{
			//Are you an idiot who decided to ban themselves? Let's hope not... ;)
			if(mb_strtolower($row['user']) == "anonymous" || mb_strtolower($row['user']) == mb_strtolower($checked_username))
			{
				print "You <b>really REALLY</b> don't want to do that. Trust me.";
				exit;
			}
			print '<article>Make note that banning using this tool will check the whole database for all IP addresses associated with this account. This will take a while if you have a <b>really</b> big database. Even then this will take forever. Just sit back.<br /><br />
			<form method="post" action="">
			<table><tr><td>			
			User:</td><td><input type="text" value="'.$row['user'].'" size="80" disabled /></td></tr>
			<tr><td>
			Reason:</td><td><input type="text" name="ban_reason" value="" size="80" /></td></tr>
			<tr><td></td><td>
			<tr><td><input type="checkbox" id="forum-ban" name="forum-ban">
			<label for="forum-ban">Ban the user from the forums as well?</label>
			<tr><td><textarea id="forum-ban-reason" name="forum-ban-reason"></textarea>
			<label for="forum-ban-reason">Reason for the forum ban?</label>
			<input type="checkbox" id="full-ban" name="full-ban">
			<label for="full-ban"> Fully ban the user? (They will no longer be able to access the site.) </label>
			<input type="submit" value="Add Ban" /></td>
			</table>
			</form>
			</article></body></html>
			';
			exit;
		}
		$ban_reason = $db->real_escape_string($_POST['ban_reason']);
		(isset($_POST['forum-ban'])) ? $do_forum_ban = true : $do_forum_ban = false;
		(isset($_POST['full-ban'] ? $full_ban = true : $full_ban = false;
		if (isset($_POST['forum-ban-reason'])
		{
			$sanitized_reason = htmlspecialchars($_POST['forum-ban-reason'])
		}
		
		//Let's grab the database values of all three since they are already there.
		$ban_id = $db->real_escape_string($row['id']);
		$ban_username = $db->real_escape_string($row['user']);
		$ban_ip = $db->real_escape_string($row['ip']);
		    
		//Full ban (or bounce) goes first and takes priority over everything else.
		if ($full_ban)
		{
			print "Applying full ban.. <br>";
			flush();
			//$query = "INSERT INTO $bounce_table (ip, user, date_added, reason) VALUES ('$ban_ip', '$checked_username', '" . time()) . "')";
			//$db->query($query);
		}
		
		//Forum ban goes second since it's optional, and we won't do it if the user skips putting it in.
		//(We don't actually have the query or the table yet.)
		if ($do_forum_ban)
		{
			if (!$sanitized_reason) 
			{
				print "Now attempting to forum ban the user... <br>";
				flush();
				//$query = "INSERT INTO $forum_ban_table(ip, user, date_added, reason) VALUES ('$ban_ip', '$checked_username', '" . time()) . ", '')";
				//$db->query($query);
			}
			
			else
			{
				print "Now attempting to forum ban the user... <br>";
				flush();
				//$query = "INSERT INTO $forum_ban_table(ip, user, date_added, reason) VALUES ('$ban_ip', '$checked_username', '" . time()) . "')", '$sanitized_reason'";
				//$db->query($query);
			}
		}

		//These queries could be done with a single join, but why bother making it complicated? 
		//Multiple simple queries shouldn't be that bad on the server... Right?
		print "Now attempting to ban IP address in user table... <br />";
		flush();
		$query = "INSERT INTO $banned_ip_table(ip,user,reason,date_added) VALUES('$ban_ip','$checked_username','$ban_reason','".time()."')";
		$db->query($query);
		
		print "Now attempting to ban IP addresses in comment_vote table... <br />";
		flush();
		$query = "SELECT * FROM $comment_vote_table WHERE user_id = '$ban_id' GROUP BY ip";
		$result = $db->query($query);
		while($row = $result->fetch_assoc())
		{
			$ban_ip = $db->real_escape_string($row['ip']);
			$query = "INSERT INTO $banned_ip_table(ip,user,reason,date_added) VALUES('$ban_ip','$checked_username','$ban_reason','".time()."')";
			$db->query($query);
		}
		
		print "Now attempting to ban IP addresses in comment table... <br />";	
		flush();		
		$query = "SELECT * FROM $comment_table WHERE user = '$ban_username' GROUP BY ip";
		$result = $db->query($query);
		while($row = $result->fetch_assoc())
		{
			$ban_ip = $db->real_escape_string($row['ip']);
			$query = "INSERT INTO $banned_ip_table(ip,user,reason,date_added) VALUES('$ban_ip','$checked_username','$ban_reason','".time()."')";
			$db->query($query);
		}	
		
		print "Now attempting to ban IP addresses in note table... <br />";
		flush();		
		$query = "SELECT * FROM $note_table WHERE user_id = '$ban_id' GROUP BY ip";
		$result = $db->query($query);
		while($row = $result->fetch_assoc())
		{
			$ban_ip = $db->real_escape_string($row['ip']);
			$query = "INSERT INTO $banned_ip_table(ip,user,reason,date_added) VALUES('$ban_ip','$checked_username','$ban_reason','".time()."')";
			$db->query($query);
		}
		
		print "Now attempting to ban IP addresses in post vote table... <br />";
		flush();		
		$query = "SELECT * FROM $post_vote_table WHERE user_id = '$ban_id' GROUP BY ip";
		$result = $db->query($query);
		while($row = $result->fetch_assoc())
		{
			$ban_ip = $db->real_escape_string($row['ip']);
			$query = "INSERT INTO $banned_ip_table(ip,user,reason,date_added) VALUES('$ban_ip','$checked_username','$ban_reason','".time()."')";
			$db->query($query);
		}		
		
		print "Now attempting to ban IP addresses in tag history table... <br />";
		flush();		
		$query = "SELECT * FROM $tag_history_table WHERE user_id = '$ban_id' GROUP BY ip";
		$result = $db->query($query);
		while($row = $result->fetch_assoc())
		{
			$ban_ip = $db->real_escape_string($row['ip']);
			$query = "INSERT INTO $banned_ip_table(ip,user,reason,date_added) VALUES('$ban_ip','$checked_username','$ban_reason','".time()."')";
			$db->query($query);
		}	
		
		print "Now attempting to ban IP addresses in post table... <br />";		
		flush();
		$query = "SELECT * FROM $post_table WHERE owner = '$ban_username' ORDER BY id DESC";
		$result = $db->query($query);
		while($row = $result->fetch_assoc())
		{
			$ban_ip = $db->real_escape_string($row['ip']);
			$query = "INSERT INTO $banned_ip_table(ip,user,reason,date_added) VALUES('$ban_ip','$checked_username','$ban_reason','".time()."')";
			$db->query($query);
		}			
	}
	else
		header('Location:../');
?>
