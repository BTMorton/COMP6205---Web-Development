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