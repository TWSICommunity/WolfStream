<?php
	session_start();
	
	$StreamIdentity = $_SESSION['StreamName'];
	$StreamKey = $_SESSION['StreamKey'];
	$StreamGame = $_SESSION['StreamGame'];
	
	$get_id = mysql_escape_string(stripslashes($_GET['streamid']));
	
	$c = mysql_connect("localhost", "user", "password");
	$getstreamvar = "SELECT * FROM `wolfstream`.`accounts` WHERE `id` = $get_id";
	$e = mysql_query($getstreamvar, $c);
	
	if($row = mysql_fetch_array($e)) {
		$StreamName1 = "{$row['stream_name']}";
		$StreamKey1 = "{$row['stream_key']}";
		$StreamGame1 = "{$row['stream_game']}";
		$StreamPass1 = "{$row['stream_pass']}"; //Differs from stream key. This is for where a streamer has to enter a password in order to watch the stream.
		$banned = (int) "{$row['banned']}";
		$banreason = "{$row['banreason']}";
		$prof = "{$row['profile_picture']}";
		$backg = "{$row['background_pg']}";
		$desc = "{$row['description']}";
		$enabledPicture = (int) "{$row['enablePicture']}";
		$enableYT = (int) "{$row['enableYT']}";
		$YouTubeMusic = "{$row['YouTubeMusicPlayer']}";
		
		if($banned == 0) {
			//Do nothing on this side...
		}else{
			die("<center><b>Welcome streamers to your own stream.</b><br/>
  <table border = '1' style = 'background-color: gray;'>
   <tr>
    <td><a href='index.php'><img src='img/button_home.png' /></a></td>
	<td><a href='tos.php'>Terms of Service</a></td>
	<td><a href='privacy.php'>Privacy of Policy</a></td>
	<td><a href='searchstream.php'>Search for a streamer</a></td>
	<td><a href='howto.php'>How to stream in this service</a></td>
   </tr>
  </table><br/>This live stream account was banned for $banreason.<center/>");
		}
		
		if($banreason == 'stream_e.stream_key_taken_away') {
			die("<center><b>Welcome streamers to your own stream.</b><br/>
  <table border = '1' style = 'background-color: gray;'>
   <tr>
    <td><a href='index.php'><img src='img/button_home.png' /></a></td>
	<td><a href='tos.php'>Terms of Service</a></td>
	<td><a href='privacy.php'>Privacy of Policy</a></td>
	<td><a href='searchstream.php'>Search for a streamer</a></td>
	<td><a href='howto.php'>How to stream in this service</a></td>
   </tr>
  </table><br/>Stream Key was taken away by an ADMIN.<center/>");
		}
		
		$_SESSION['StreamName_Session'] = $StreamName1;
		$_SESSION['StreamKey_Session'] = $StreamKey1;
		$_SESSION['StreamGame_Session'] = $StreamGame1;
	}
	mysql_close($c);
	$StreamIdentity2 = $_SESSION['StreamName_Session'];
	$StreamKey2 = $_SESSION['StreamKey_Session'];
	$StreamGame2 = $_SESSION['StreamGame_Session'];
	
	if($_SESSION['timetill_PASSWORDRESET_'.$StreamName1] <= time()) {
		unset($_SESSION['StreamPassword_'.$StreamName1]);
	}
	
	if(isset($_POST['enterPASS'])) {
		$streampassword = mysql_escape_string(stripslashes(hash('sha512', $_POST['strmpass'])));
		
		if($streampassword == $StreamPass1) {
			$_SESSION['StreamPassword_'.$StreamName1] = $streampassword;
		}else{
			die("Failed to enter the correct password. Only users who know the password may enter. <a href='watchstream.php?streamid=$get_id'>Click</a> to retry again.");
		}
		
		$_SESSION['timetill_PASSWORDRESET_'.$StreamName1] = time()+10;
	}
	
	if($StreamPass1 == "cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e") {
		$_SESSION['StreamPassword_'.$StreamName1] = "cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e";
	}
	mysql_close($c);
	//echo "Time Till Password Reset: ".$_SESSION['timetill_PASSWORDRESET_'.$StreamName1]."<br/>Time Currently Displayed: ".time();
?>
<html>
 <head>
  <title>Wolf Stream Online RTMP Service</title>
 </head>
 <body onload='restrainIllegalOperation()' oncontextmenu="return false">
  <img src="<?php echo $backg; ?>" style="position:fixed; width: 100%; height: 100%; top:0px; left:0px; z-index:-9999;" />
  <center><b>Welcome streamers to your own stream.</b><br/>
  <table border = '1' style = 'background-color: gray;'>
   <tr>
    <td><a href='index.php'><img src='img/button_home.png' /></a></td>
    <td><?php if($StreamIdentity == ""){ echo "<a href='login.php'>Login</a>"; }else{echo "Welcome, $StreamIdentity";} ?></td>
	<td><?php if($StreamIdentity == ""){ echo "<a href='register.php'>Register</a>"; }else{echo "Already Registered. <a href='logout.php'>Logout</a>";} ?></td>
	<td><?php if($StreamIdentity == ""){ echo "---"; }else{echo "<a href='editstream.php'>Edit your stream account</a>";} ?></td>
	<td><a href='tos.php'>Terms of Service</a></td>
	<td><a href='privacy.php'>Privacy of Policy</a></td>
	<td><a href='searchstream.php'>Search for a streamer</a></td>
	<td><a href='howto.php'>How to stream in this service</a></td>
	<td><a href='report.php'>Report a streamer</a></td>
   </tr>
  </table>
  <br/><center/>
  <?php 
  
  if(!$_SESSION['StreamPassword_'.$StreamName1]) {
	  echo "<br/><b>This stream is private!</b><br/><form action='' method='POST'>Enter the streamer's password: <input type='password' name='strmpass' /><input type='submit' name='enterPASS' value='Enter password' /></form><center/>";
  }else{
	  echo '<b>Currently Streaming a game: '.$StreamGame2.'</b><br/>';
	  echo '<object width="560" height="400"> <param name="movie" value="http://hosting-marketers.com/strobe/StrobeMediaPlayback.swf"></param><param name="flashvars" value="streamType=Live&autoPlay=false&scaleMode=letterbox&loop=true&backgroundColor=000000&optimizeBuffering=true&initialBufferTime=0.1&expandedBufferTime=10&minContinuousPlaybackTime=30&src=rtmp://server.twsicommunity.com/'.$StreamIdentity2.'/'.$StreamKey2.'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://hosting-marketers.com/strobe/StrobeMediaPlayback.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"  width="560" height="400" flashvars="streamType=Live&autoPlay=false&scaleMode=letterbox&loop=true&backgroundColor=000000&optimizeBuffering=true&initialBufferTime=0.1&expandedBufferTime=10&minContinuousPlaybackTime=30&src=rtmp://server.twsicommunity.com/'.$StreamIdentity2.'/'.$StreamKey2.'"></embed></object><br/>
	  <div style="opacity:0.8;"><p style="background-color: gray; border: 2px ridge black;">'.$desc.'</p></div>';
	  if($enabledPicture == 1) {
		  echo '<img src="'.$prof.'" style="position: fixed; width: 96px; height: 96px; top: 135px; left: 1110px; z-index:99999; opacity: 0.8;" />';
	  }
	  if($enableYT == 1) {
		echo '<iframe width="320" height="100" style="visibility:hidden;" src="https://www.youtube.com/embed/'.$YouTubeMusic.'?version=1&autoplay=1&loop=1" frameborder="0" allowfullscreen></iframe>';
	  }
  }
  
  ?>
	  </body>
	 </html>
