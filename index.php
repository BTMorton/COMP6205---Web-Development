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
		<script type="text/javascript">
			var peaks_inst;
			var playReady = false;

			function loadPeaks() {
				requirejs.config({
					paths: {
						peaks: 'bower_components/peaks.js/src/main',
						EventEmitter: 'bower_components/eventemitter2/lib/eventemitter2',
						Kinetic: 'bower_components/kineticjs/kinetic',
						'waveform-data': 'bower_components/waveform-data/dist/waveform-data.min'
					}
				});

				// requires it
				require(['peaks'], function (Peaks) {
					peaks_inst = Peaks.init({
						container: document.querySelector('#peaks_container'),
						mediaElement: document.querySelector('#peaks_player'),
						height: 200,
						zoomLevels: [512, 1024, 2048, 4096],
						keyboard: true,
						overviewWaveformColor: "#d1e751",
						zoomWaveformColor: "rgba(0, 0, 0, 0)"
					});

					peaks_inst.on('segments.ready', function(){
						$("#progress").addClass("hidden");
						$("#peaks_container").removeAttr("style");
						playReady = true;
						// do something when segments are ready to be displayed
					});
				});
			}

			$(function() {
				$(window).scroll(function() {
					if ($(window).scrollTop() >= 165) {
					   $('header').addClass('fixed');
					} else {
					   $('header').removeClass('fixed');
					}

					if ($(window).scrollTop() > ($("#about").offset().top - $("#about h2").height())) {
						$("header .nav a:not(.about).active").removeClass("active");
						$("header .nav .about").addClass("active");
					} else if ($(window).scrollTop() > ($("#tutorials").offset().top - $("#tutorials h2").height())) {
						$("header .nav a:not(.tuts).active").removeClass("active");
						$("header .nav .tuts").addClass("active");
					} else {
						$("header .nav a:not(.edit).active").removeClass("active");
						$("header .nav .edit").addClass("active");
					}
				});

				$('#file_upload').fileupload({
					url: './upload.php',
					dataType: 'json',
					start: function (e, data) {
						$("#peaks_container").addClass("hidden");
						$("#placeholder").addClass("hidden");
						$("#progress").removeClass("hidden").removeClass("error");
						$("#peaks_player")[0].pause();
						playReady = false;
					},
					done: function (e, data) {
						console.log(data.result);
						if (data.result.file_name != "" && data.result.file_name != undefined) {
							$("#peaks_container").removeClass("hidden").css("height", '0').css('opacity', '0');
							$("#progress_text").text("Uploaded. Generating waveform...");;
							$("#peaks_player")[0].src = data.result.file_name;
							$("#peaks_player")[0].load();
							loadPeaks();
						} else {
							$("#progress").addClass("error");
							$("#progress_text").text("Error: "+data.result.error)
						}
					},
					progressall: function (e, data) {
						var progress = parseInt(data.loaded / data.total * 100, 10);
						$("#progress_text").text("Progress: " + progress + "%");
						$("#progress_bar").width(progress+"%");
						console.log("Progress: "+progress);
					}
				}).prop('disabled', !$.support.fileInput)
					.parent().addClass($.support.fileInput ? undefined : 'disabled');

				$("#play_button").click(function() {
					if (playReady) {
						if ($("#peaks_player")[0].paused) {
							$("#peaks_player")[0].play();
						} else {
							$("#peaks_player")[0].pause();
						}
					}
				});

				$("#peaks_player").on("play", function() {
					$("#play_button").addClass("pause");
				}).on("pause", function() {
					$("#play_button").removeClass("pause");
				}).on("ended", function() {
					$("#play_button").removeClass("pause");
				});

			    $("#trim").click(function() {
			    	peaks_inst.segments.add([{
			    		startTime: peaks_inst.time.getCurrentTime(),
			    		endTime: peaks_inst.time.getCurrentTime() + 10,
			    		editable: true,
			    		color: '#000000',
			    		labelText: 'Trim'
			    	}]);
			    });

			    $("#fade_in").click(function() {
			    	peaks_inst.segments.add([{
			    		startTime: 0.1,
			    		endTime: 10,
			    		editable: [false, true],
			    		color: '#26ade4',
			    		labelText: 'Fade In'
			    	}]);
			    });

			    $("#fade_out").click(function() {
			    	peaks_inst.segments.add([{
			    		startTime: peaks_inst.player.duration - 10,
			    		endTime: peaks_inst.player.duration,
			    		editable: [true, false],
			    		color: '#26ade4',
			    		labelText: 'Fade In'
			    	}]);
			    });

			    $("#stretch").click(function() {
			    	peaks_inst.segments.add([{
			    		startTime: peaks_inst.time.getCurrentTime(),
			    		endTime: peaks_inst.time.getCurrentTime() + 10,
			    		editable: true,
			    		color: 'red',
			    		labelText: 'Stretch'
			    	}]);
			    });
			});
		</script>
	</head>
	<body>
		<header id="editor_jump">
			<div class="container">
				<h1>Editing</h1>
				<div class="upload_text">
					<input type="file" name="file_upload" id="file_upload" />
					upload file to edit
				</div>
				<div class="nav">
					<a href="#editor_jump" class="edit active">Edit</a><a href="#tutorials_jump" class="tuts">Tutorials</a><a href="#about_jump" class="about">About</a>
				</div>
			</div>
		</header>
		<div id="wrapper">
			<div id="editor" class="container">
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
				<div class="controls">
					<div class="control" id="trim"><h3>Trim</h3>Select an area of the audio to cut</div>
					<div class="control" id="fade_in"><h3>Fade In</h3>Select an area of the audio to fade in</div>
					<div class="control" id="fade_out"><h3>Fade Out</h3>Select an area of the audio to fade out</div>
					<div class="control" id="stretch"><h3>Stretch</h3>Select an area of the audio to stretch</div>
					<div class="control" id="share"><h3>Share</h3>Generate a link to share your audio file</div>
					<div class="control" id="save"><h3>Save</h3>Download your edited audio file</div>
				</div>
			</div>
			<hr id="tutorials_jump" />
			<div id="tutorials" class="container">
				<h2>Tutorials</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit velit eget placerat faucibus. Maecenas a mollis ipsum. Nam non nulla sit amet orci pellentesque aliquam bibendum luctus est. Curabitur vestibulum viverra nisl, non ultricies diam euismod quis. Morbi eget interdum nibh. Donec non ultricies sapien, at cursus sem. Vivamus placerat condimentum tortor, in scelerisque turpis lobortis sed. Ut lacinia, magna vel tristique feugiat, eros est sollicitudin purus, in gravida neque leo ut magna. Nulla facilisis tempor bibendum. Proin mi erat, suscipit eu turpis in, tristique venenatis lacus. Phasellus sit amet odio tincidunt, vestibulum libero ut, sollicitudin eros.</p>
				<div class="stories">
					<img src="img/holding.png">
					<h3>1. Share</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit velit eget placerat faucibus. Maecenas a mollis ipsum. Nam non nulla sit amet orci pellentesque aliquam bibendum luctus est. Curabitur vestibulum viverra nisl, non ultricies diam euismod quis. Morbi eget interdum nibh. Donec non ultricies sapien, at cursus sem. Vivamus placerat condimentum tortor, in scelerisque turpis lobortis sed. Ut lacinia, magna vel tristique feugiat, eros est sollicitudin purus, in gravida neque leo ut magna. Nulla facilisis tempor bibendum. Proin mi erat, suscipit eu turpis in, tristique venenatis lacus. Phasellus sit amet odio tincidunt, vestibulum libero ut, sollicitudin eros.</p>
					<div class="clear"></div>

					<img src="img/holding.png">
					<h3>2. Trim</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit velit eget placerat faucibus. Maecenas a mollis ipsum. Nam non nulla sit amet orci pellentesque aliquam bibendum luctus est. Curabitur vestibulum viverra nisl, non ultricies diam euismod quis. Morbi eget interdum nibh. Donec non ultricies sapien, at cursus sem. Vivamus placerat condimentum tortor, in scelerisque turpis lobortis sed. Ut lacinia, magna vel tristique feugiat, eros est sollicitudin purus, in gravida neque leo ut magna. Nulla facilisis tempor bibendum. Proin mi erat, suscipit eu turpis in, tristique venenatis lacus. Phasellus sit amet odio tincidunt, vestibulum libero ut, sollicitudin eros.</p>
					<div class="clear"></div>

					<img src="img/holding.png">
					<h3>2. Smooth Transition</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit velit eget placerat faucibus. Maecenas a mollis ipsum. Nam non nulla sit amet orci pellentesque aliquam bibendum luctus est. Curabitur vestibulum viverra nisl, non ultricies diam euismod quis. Morbi eget interdum nibh. Donec non ultricies sapien, at cursus sem. Vivamus placerat condimentum tortor, in scelerisque turpis lobortis sed. Ut lacinia, magna vel tristique feugiat, eros est sollicitudin purus, in gravida neque leo ut magna. Nulla facilisis tempor bibendum. Proin mi erat, suscipit eu turpis in, tristique venenatis lacus. Phasellus sit amet odio tincidunt, vestibulum libero ut, sollicitudin eros.</p>
					<div class="clear"></div>
				</div>
			</div>
			<hr id="about_jump" />
			<div id="about" class="container">
				<h2>About</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit velit eget placerat faucibus. Maecenas a mollis ipsum. Nam non nulla sit amet orci pellentesque aliquam bibendum luctus est. Curabitur vestibulum viverra nisl, non ultricies diam euismod quis. Morbi eget interdum nibh. Donec non ultricies sapien, at cursus sem. Vivamus placerat condimentum tortor, in scelerisque turpis lobortis sed. Ut lacinia, magna vel tristique feugiat, eros est sollicitudin purus, in gravida neque leo ut magna. Nulla facilisis tempor bibendum. Proin mi erat, suscipit eu turpis in, tristique venenatis lacus. Phasellus sit amet odio tincidunt, vestibulum libero ut, sollicitudin eros.</p>
				<div class="name">
					<div class="photo ben"></div>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit velit eget placerat faucibus. Maecenas a mollis ipsum. Nam non nulla sit amet orci pellentesque aliquam bibendum luctus est. Curabitur vestibulum viverra nisl, non ultricies diam euismod quis. Morbi eget interdum nibh. Donec non ultricies sapien, at cursus sem. Vivamus placerat condimentum tortor, in scelerisque turpis lobortis sed. Ut lacinia, magna vel tristique feugiat, eros est sollicitudin purus, in gravida neque leo ut magna. Nulla facilisis tempor bibendum. Proin mi erat, suscipit eu turpis in, tristique venenatis lacus. Phasellus sit amet odio tincidunt, vestibulum libero ut, sollicitudin eros.</p>
				</div><div class="name">
					<div class="photo simon"></div>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit velit eget placerat faucibus. Maecenas a mollis ipsum. Nam non nulla sit amet orci pellentesque aliquam bibendum luctus est. Curabitur vestibulum viverra nisl, non ultricies diam euismod quis. Morbi eget interdum nibh. Donec non ultricies sapien, at cursus sem. Vivamus placerat condimentum tortor, in scelerisque turpis lobortis sed. Ut lacinia, magna vel tristique feugiat, eros est sollicitudin purus, in gravida neque leo ut magna. Nulla facilisis tempor bibendum. Proin mi erat, suscipit eu turpis in, tristique venenatis lacus. Phasellus sit amet odio tincidunt, vestibulum libero ut, sollicitudin eros.</p>
				</div>
			</div>
		</div>
		<footer>
			<div class="container">
				<h3>COMP6205 - Web Development</h3>
				<div class="nav">
					<h4>Navigation</h4>
					<a href="#editor">Edit</a><br />
					<a href="#tutorials">Tutorials</a><br />
					<a href="#about">About</a><br />
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