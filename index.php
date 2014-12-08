<?php

if (!empty($_GET['segments'])) {
	require_once("signedjsonwebtoken.php");

	$key = 'uCSMdetDReY4MAyVTh7fuq7hZ3tYk9Gyc3GZWpCR3LWkSgmHm9bwzWR2pbnTMzUecuR4mAMj';

	$signer = new SignedJSONWebToken($key);
	$content = $signer->unsign($_GET['segments']);
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Ben Morton (bm12g10) and Simon Bidwell (sab3g11) Web Development Coursework</title>
		<link rel="stylesheet" href="css/reset.min.css" />
		<link rel="stylesheet" href="css/muli.css" type="text/css" />
		<link rel="stylesheet" href="bower_components/jquery-ui/themes/smoothness/jquery-ui.css" />
		<link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css" type="text/css" />
		<link rel="stylesheet" href="css/jquery.fileupload.css">
		<link rel="stylesheet" href="css/style.css" />
		<script src="bower_components/jquery/dist/jquery.min.js"></script>
		<script src="bower_components/jquery-ui/jquery-ui.min.js"></script>
		<script src="js/jquery.fileupload.js"></script>
		<script src="bower_components/requirejs/require.js"></script>
		<script src="js/scripts.js" type="text/javascript"></script>
		<?php if (isset($content) & !empty($content)) { ?>
		<script type="text/javascript">
			var presetSegments = <?=$content?>;
			$(function() {
				loadPreset();
			});
		</script>
		<? } ?>
	</head>
	<body>
		<div id="blackout" style="display: none;">
			<div id="share_box">
				<textarea id="txt_share" readonly="readonly"></textarea>
				<a href="#" id="close_share">Close</a>
			</div>
		</div>
		<div id="header_wrap">
			<header>
				<div class="container">
					<h1>Editing</h1>
					<div class="upload_text">
						<input type="file" name="file_upload" id="file_upload" />
						upload file to edit
					</div>
					<div class="nav">
						<a href="#header_wrap" class="edit active">Edit</a><a href="#tutorials_jump" class="tuts">Tutorials</a><a href="#about_jump" class="about">About</a>
					</div>
				</div>
			</header>
		</div>
		<div id="wrapper">
			<div id="editor" class="container">
				<div id="player_container">
					<div id="play_button"></div>
					<div id="placeholder">
						Use the uploader above to upload your file and begin editing!
					</div>
					<div id="progress" class="hidden">
						<div id="progress_bar"></div>
						<div id="progress_text"></div>
					</div>
					<div id="peaks_container" class="hidden"></div>
					<audio id="peaks_player"></audio>
				</div>
				<div class="controls">
					<div class="control" id="trim"><h3>Trim</h3>Select an area of the audio to cut</div>
					<div class="control" id="silence"><h3>Silence</h3>Select an area of the audio to silence</div>
					<div class="control" id="fade_in"><h3>Fade In</h3>Select an area of the audio to fade in</div>
					<div class="control" id="fade_out"><h3>Fade Out</h3>Select an area of the audio to fade out</div>
					<div class="control" id="slow"><h3>Slow Down</h3>Select an area of the audio to slow down</div>
					<div class="control" id="fast"><h3>Speed Up</h3>Select an area of the audio to speed up</div>
					<div class="control" id="share"><h3>Share</h3>Generate a link to share your audio file</div>
					<div class="control" id="save"><div class="spinner" style="display: none;"></div><h3>Save</h3>Download your edited audio file</div>
				</div>
			</div>
			<hr id="tutorials_jump" />
			<div id="tutorials" class="container">
				<h2>Tutorials</h2>
				<p>The Editing web application features a number of functions. First and foremost Editing allows a user to Upload an MP3 audio file, and after an audio file has been uploaded, users can Trim, Fade in, Fade Out, Stretch, Share and Save the file.</p>
				<div class="stories">
					<div class="video" data-filename="videos/upload.mp4"></div>
					<h3>1. Uploading an Audio File</h3>
					<p>For Editing to work, an audio file has to be provided. To upload an audio file, click the on the text "upload file to edit" or the upload button in the header at the top of the page. After clicking to upload an audio file, a file selector will open. Using the file selector, navigate to and select the audio file to be edited and then click upload. Once the file upload is completed, the audio player on the homepage of Editing will display the waveform of your audio file ready for editing. Note: Editing only works with mp3 audio files.</p>
					<div class="clear"></div>

					<div class="video" data-filename="videos/trim.mp4"></div>
					<h3>2. Trim</h3>
					<p>A trim will cut any noise within the selected audio range, meaning that the selected range will be entirely deleted. To apply the trim function, first, click on the waveform to indicate a beginning timestamp for the trim. Next, press the trim button underneath the audio player to apply the trim function to the waveform beginning from the selected timestamp. Applying the trim function by pressing the button will add a segment to the waveform. The segment indicates the audio being trimmed and the segment handles can be dragged to change the length of the trim. </p>
					<div class="clear"></div>
					
					<div class="video" data-filename="videos/silence.mp4"></div>
					<h3>3. Silence</h3>
					<p>The Silence function will take a selected section of audio and mute the volume for that section. A silence is different from a trim because a trim will skip the trimmed section whereas a silence will play no sound for the silenced section. To apply a silence, click on the waveform where the silence should begin, press the silence button, and then adjust the length of the silence by dragging the silence segment handles.</p>
					<div class="clear"></div>
	
					<div class="video" data-filename="videos/fadein.mp4"></div>
					<h3>4. Fade in</h3>
					<p>A fade in gradually increases the volume level of an audio signal such that it reaches it's true volume level starting from silence at the beginning. With the Editing audio editor, Fade in's are always applied to the start of an audio file. To apply a Fade in function using Editing, press the Fade In button underneath the audio player; this will create a fade in segment on the waveform in the audio player. The segment handles can then be dragged to adjust the length of the fade in function. </p>
					<div class="clear"></div>
					
					<div class="video" data-filename="videos/fadeout.mp4"></div>
					<h3>5. Fade out</h3>
					<p>A fade out gradually decreases the volume level of an audio signal such that it is reduced to silence at it's end. With the Editing audio editor, Fade outs' are always applied to the end of an audio file. To apply a Fade out function using Editing, press the Fade out button underneath the audio player; this will create a fade out segment on the waveform in the audio player. The segment handles can then be dragged to adjust the length of the fade out function.</p>
					<div class="clear"></div>
	
					<div class="video" data-filename="videos/slowdown.mp4"></div>
					<h3>6. Slow down</h3>
					<p>A slow down changes the speed of a selected audio segment by a given multiplier to give the effect of a slower audio segment. To apply the slow down function, first, click on the waveform to indicate a beginning timestamp for the slow down. Next, press the slow down button underneath the audio player to apply the slow down function to the waveform beginning from the selected timestamp. Applying the slow down function by pressing the button will add a segment to the waveform. The segment indicates the audio being slowed and the segment handles can be dragged to change the length of the slow.</p>
					<div class="clear"></div>

					<div class="video" data-filename="videos/speedup.mp4"></div>
					<h3>7. Speed up</h3>
					<p>A speed up changes the speed of a selected audio segment by a given multiplier to give the effect of a faster audio segment. To apply a speed up function, click on the waveform where the speed up should begin, press the speed up button, and then adjust the length of the speed up by dragging the speed up segment handles.</p>
					<div class="clear"></div>

					<div class="video" data-filename="videos/share.mp4"></div>
					<h3>8. Share</h3>
					<p>After a file has been uploaded to Editing, it can be accessed on a unique url. This unique url can be used to share the audio file with other users. To get the unique url for the current audio file being used on Editing, click the Share button underneath the audio player and the unique url will be displayed so it can be copied to the clipboard.</p>
					<div class="clear"></div>

					<div class="video" data-filename="videos/upload.mp4"></div>
					<h3>9. Save</h3>
					<p>Saving a file on Editing allows the user to download their edited audio file from the server. After a user is happy with their edited file, clicking the Save button beneath the audio player, will initiate a file transfer to download the audio file to the user's local file space.</p>
					<div class="clear"></div>
				</div>
			</div>
			<hr id="about_jump" />
			<div id="about" class="container">
				<h2>About</h2>
				<p>Editing is a HTML5 web application created by <a href="mailto:bm12g10@soton.ac.uk">Ben Morton</a> and <a href="mailto:sab3g11@soton.ac.uk">Simon Bidwell</a> for COMP6205: Web Development. The tool makes use of javascript, php and HTML5 Audio to create an audio editing application with sharing, saving and live playback.</p> 
				<p>Due to its responsive design that adapts to the users viewpoint and intuitive user interface, Editing can be used on a wide variety of devices. After uploading an audio file, users can trim, fade in, fade out, stretch, share and export their file, regardless of their access device, allowing for quick and easy audio editing in an easily accessible, web application. </p>
				<div class="name">
					<div class="photo ben"></div>
					<p>Ben is a 4th year Computer Science MEng student, currently studying at the University of Southampton. He is a keen software developer with large amounts of experience in various languages, as well as having a large interest in media, working with the Students' Union in audio and video mixing and editing. Ben has taken on many part time jobs working as a Web Developer and has also worked in server administration, being entrusted with setting up various types of servers.</p>
				</div><div class="name">
					<div class="photo simon"></div>
					<p>Simon is a 4th year Computer Science MENg student with a passion for web design, programming and sports, studying at The University of Southampton. Two years ago Simon interned as a Research Assistant for the Electronics and Computer Science Department at The University of Southampton, and in the summer of 2014 he was a Summer Student for the LHCb project at CERN, Geneva, working specifically on the DIRAC Grid Computing Solution.</p>
				</div>
			</div>
		</div>
		<footer>
			<div class="container">
				<h3>COMP6205 - Web Development</h3>
				<div class="nav">
					<h4>Navigation</h4>
					<a href="#header_wrap">Edit</a><br />
					<a href="#tutorials_jump">Tutorials</a><br />
					<a href="#about_jump">About</a><br />
				</div>
				<div class="contact">
					<h4>Contact</h4>
					<a href="mailto:bm12g10@soton.ac.uk">Ben Morton</a><br />
					<a href="mailto:sab3g11@soton.ac.uk">Simon Bidwell</a>
				</div>
			</div>
		</footer>
	</body>
</html>
