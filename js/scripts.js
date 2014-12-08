var peaks_inst;
var playReady = false;
var presetSegments;

function getJSON() {
	var segments = peaks_inst.segments.getSegments();
	var json = { 'filename': $("#peaks_player")[0].src, 'fade_in' : [], 'fade_out': [], 'trim' : [], 'silence': [], 'slow': [], 'fast': [] };

	for (var x in segments) {
		var segment = segments[x];

		var dets = { 'start': segment.startTime, 'end': segment.endTime };

		switch (segment.labelText) {
			case 'Fade In':
				json.fade_in.push(dets);
				break;
			case 'Fade Out':
				json.fade_out.push(dets);
				break;
			case 'Trim':
				json.trim.push(dets);
				break;
			case 'Silence':
				json.silence.push(dets);
				break;
			case 'Slow Down':
				json.slow.push(dets);
				break;
			case 'Speed Up':
				json.fast.push(dets);
				break;
		}
	}

	return json;
}

function loadPreset() {
	$("#peaks_player")[0].src = presetSegments.filename;
	$("#peaks_player")[0].load();
	$("#placeholder").addClass("hidden");
	$("#progress").removeClass("hidden").removeClass("error");
	$("#progress_text").text("Generating waveform...");
	$("#peaks_container").removeClass("hidden").css("height", '0').css('opacity', '0');
	loadPeaks();
}

function loadPresetSegments() {
	for (var x in presetSegments.trim) {
    	peaks_inst.segments.add([{
    		startTime: presetSegments.trim[x].start,
    		endTime: presetSegments.trim[x].end,
    		editable: true,
    		color: '#000000',
    		labelText: 'Trim'
    	}]);
    }

    for (var x in presetSegments.silence) {
    	peaks_inst.segments.add([{
    		startTime: presetSegments.silence[x].start,
    		endTime: presetSegments.silence[x].end,
    		editable: true,
    		color: '#999999',
    		labelText: 'Silence'
    	}]);
    }

    for (var x in presetSegments.fade_in) {
    	peaks_inst.segments.add([{
    		startTime: presetSegments.fade_in[x].start,
    		endTime: presetSegments.fade_in[x].end,
    		editable: [false, true],
    		color: '#26ade4',
    		labelText: 'Fade In'
    	}]);
    }

    for (var x in presetSegments.slow) {
    	peaks_inst.segments.add([{
    		startTime: presetSegments.slow[x].start,
    		endTime: presetSegments.slow[x].end,
    		editable: true,
    		color: 'red',
    		labelText: 'Slow Down'
    	}]);
    }

    for (var x in presetSegments.fast) {
    	peaks_inst.segments.add([{
    		startTime: presetSegments.fast[x].start,
    		endTime: presetSegments.fast[x].end,
    		editable: true,
    		color: 'orange',
    		labelText: 'Speed Up'
    	}]);
    }

    for (var x in presetSegments.fade_out) {
    	peaks_inst.segments.add([{
    		startTime: presetSegments.fade_out[x].start,
    		endTime: presetSegments.fade_out[x].end,
    		editable: [true, false],
    		color: '#26ade4',
    		labelText: 'Fade Out'
    	}]);
    }

	presetSegments = undefined;
}

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
			if (presetSegments != undefined) {
				loadPresetSegments();
			}
			// do something when segments are ready to be displayed
		});

	    peaks_inst.on('player_time_update', function() {
	    	var curSegments = this.segments.getCurrentSegment(), time = this.time.getCurrentTime(), stretch = false, trimmed = false, fading = false;

	    	for (var x in curSegments) {
	    		segment = curSegments[x];

	    		switch (segment.labelText) {
	    			case "Fade In":
	    				var ftime = time - segment.startTime, duration = segment.endTime - segment.startTime;
	    				var volume = ((ftime * ftime) / duration) / duration;
	    				$("#peaks_player")[0].volume = volume;
	    				fading = true;
	    				break;
	    			case "Trim":
	    				this.player.seekBySeconds(segment.endTime);
	    				break;
	    			case "Silence":
	    				$("#peaks_player")[0].volume = 0;
	    				fading = true;
	    				break;
    				case "Fade Out":
	    				var ftime = time - segment.startTime, duration = segment.endTime - segment.startTime;
	    				var volume = (duration - ((ftime * ftime) / duration)) / duration;
	    				$("#peaks_player")[0].volume = volume;
	    				fading = true;
    					break;
    				case "Slow Down":
    					$("#peaks_player")[0].playbackRate = 0.5;
    					stretch = true;
    					break;
    				case "Speed Up":
    					$("#peaks_player")[0].playbackRate = 2;
    					stretch = true;
    					break;
	    		}
	    	}

	    	if (!fading) $("#peaks_player")[0].volume = 1;
	    	if (!stretch) $("#peaks_player")[0].playbackRate = 1;
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

		if ($(window).scrollTop() > ($("#about").offset().top - $("#about h2").height() - 35)) {
			$("header .nav a:not(.about).active").removeClass("active");
			$("header .nav .about").addClass("active");
		} else if ($(window).scrollTop() > ($("#tutorials").offset().top - $("#tutorials h2").height() - 35)) {
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
    	if (playReady) {
	    	peaks_inst.segments.add([{
	    		startTime: peaks_inst.time.getCurrentTime(),
	    		endTime: peaks_inst.time.getCurrentTime() + 10,
	    		editable: true,
	    		color: '#000000',
	    		labelText: 'Trim'
	    	}]);
	    }
    });

    $("#silence").click(function() {
    	if (playReady) {
	    	peaks_inst.segments.add([{
	    		startTime: peaks_inst.time.getCurrentTime(),
	    		endTime: peaks_inst.time.getCurrentTime() + 10,
	    		editable: true,
	    		color: '#999999',
	    		labelText: 'Silence'
	    	}]);
	    }
    });

    $("#fade_in").click(function() {
    	if (playReady) {
	    	peaks_inst.segments.add([{
	    		startTime: 0,
	    		endTime: 10,
	    		editable: [false, true],
	    		color: '#26ade4',
	    		labelText: 'Fade In'
	    	}]);
	    }
    });

    $("#fade_out").click(function() {
    	if (playReady) {
	    	peaks_inst.segments.add([{
	    		startTime: $("#peaks_player")[0].duration - 10,
	    		endTime: $("#peaks_player")[0].duration,
	    		editable: [true, false],
	    		color: '#26ade4',
	    		labelText: 'Fade Out'
	    	}]);
	    }
    });

    $("#slow").click(function() {
    	if (playReady) {
	    	peaks_inst.segments.add([{
	    		startTime: peaks_inst.time.getCurrentTime(),
	    		endTime: peaks_inst.time.getCurrentTime() + 10,
	    		editable: true,
	    		color: 'red',
	    		labelText: 'Slow Down'
	    	}]);
	    }
    });

    $("#fast").click(function() {
    	if (playReady) {
	    	peaks_inst.segments.add([{
	    		startTime: peaks_inst.time.getCurrentTime(),
	    		endTime: peaks_inst.time.getCurrentTime() + 10,
	    		editable: true,
	    		color: 'orange',
	    		labelText: 'Speed Up'
	    	}]);
	    }
    });

    $("#share").click(function() {
    	if (playReady) {
	    	$.post("generate_share_url.php", { segments: JSON.stringify(getJSON()) }, function (url) {
	    		$("#blackout").show();
	    		$("#txt_share").text(url).focus().select();
	    	}, 'text');
    	}
    });

    $("#close_share").click(function(e) {
    	$("#blackout").hide();
    	$("#txt_share").text('');
    	e.preventDefault();
    	return false;
    });

	$("#save").click(function() {
		if (playReady) {
			if (!$(".spinner", this).is(":visible")) {
				$("#save .spinner").show();
				$('body').append('<form action="save_file.php" method="post" target="save_frame" id="postToIframe"></form>');
			    $('#postToIframe').append('<input type="hidden" name="segments" value=\''+JSON.stringify(getJSON())+'\' />');
			    $('#postToIframe').submit().remove();
			}
		}
	});

	$("#save_frame").load(function() {
		$("#save .spinner").hide();

		if ($(this).contentType != "application/octet-stream") {
			console.log($("body", this).text());
			$("#save").addClass("error");
		}
	});
});