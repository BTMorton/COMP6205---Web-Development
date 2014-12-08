<?php
ob_start();

function decode_mp3($in_file, $out_file = "", $unlink = false) {
	if (empty($out_file)) $out_file = dirname($in_file)."/".basename($in_file, ".mp3").".wav";

	echo "Decoding: ".$in_file." -> ".$out_file."\n";
	exec("sox ".escapeshellarg($in_file)." ".escapeshellarg($out_file)." gain -h", $dec_out, $dec_result);

	if ($dec_result == 0) {
		return true;
	} else {
		return false;
	}
}

function encode_mp3($in_file, $out_file = "", $unlink = false) {
	if (empty($out_file)) $out_file = dirname($in_file)."/".basename($in_file, ".wav").".mp3";

	echo "Encoding: ".$in_file." -> ".$out_file."\n";
	exec("lame --quiet -h -b192 ".escapeshellarg($in_file)." ".escapeshellarg($out_file), $enc_out, $enc_result);

	if ($enc_result == 0) {
		if ($unlink) unlink($in_file);
		return true;
	} else {
		return false;
	}
}


function trim_file($in_file, $start = 0, $end = 0, $out_file = "", $unlink = false) {
	$empt_out = false;

	if (empty($out_file)) {
		$empt_out = true;
		$out_file = tempnam("/tmp/", basename($in_file, ".wav"));
		rename($out_file, $out_file.".wav");
		$out_file .= ".wav";
	}

	$length = $end - $start;

	if ($length <= 0) return false;

	$stime = sprintf("%d:%02f", (int)($start / 60), ($start % 60));
	$etime = sprintf("%d:%02f", (int)($end / 60), ($end % 60));
	$ltime = sprintf("%d:%02f", (int)($length / 60), ($length % 60));

	exec("sox ".escapeshellarg($in_file)." ".escapeshellarg($out_file)." trim 0 ".escapeshellarg($stime)." ".escapeshellarg($ltime), $trim_out, $trim_result);

	if ($trim_result == 0) {
		if ($empt_out) {
			unlink($in_file);
			rename($out_file, $in_file);
		} elseif ($unlink) unlink($in_file);

		return true;
	} else {
		return false;
	}
}

function change_rate($in_file, $speed, $start = 0, $end = 0, $out_file = '', $unlink = false) {
	$empt_out = false;
	
	if (empty($out_file)) {
		$empt_out = true;
		$out_file = tempnam("/tmp/", basename($in_file, ".wav"));
		rename($out_file, $out_file.".wav");
		$out_file .= ".wav";
	}

	if ($end  == 0) $end = exec("soxi -D ".escapeshellarg($in_file));

	$length = $end - $start;

	if ($length <= 0) return false;

	$stime = escapeshellarg(sprintf("%d:%02f", (int)($start / 60), ($start % 60)));
	$etime = escapeshellarg(sprintf("%d:%02f", (int)($end / 60), ($end % 60)));
	$ltime = escapeshellarg(sprintf("%d:%02f", (int)($length / 60), ($length % 60)));

	$clin_file = escapeshellarg($in_file);
	$clout_file = escapeshellarg($out_file);
	exec ('sox -t sox "|sox '.$clin_file.' -p trim 0 '.$stime.'" -t sox "|sox '.$clin_file.' -p trim '.$stime.' '.$ltime.' tempo '.(float)$speed.'" -t sox "|sox '.$clin_file.' -p trim '.$etime.'" '.$clout_file, $trim_out, $trim_result);

	if ($trim_result == 0) {
		if ($empt_out) {
			unlink($in_file);
			rename($out_file, $in_file);
		} elseif ($unlink) unlink($in_file);

		return true;
	} else {
		return false;
	}
}

function silence($in_file, $start = 0, $end = 0, $out_file = '', $unlink = false) {
	$empt_out = false;

	if (empty($out_file)) {
		$empt_out = true;
		$out_file = tempnam("/tmp/", basename($in_file, ".wav"));
		rename($out_file, $out_file.".wav");
		$out_file .= ".wav";
	}

	if ($end  == 0) $end = exec("soxi -D ".escapeshellarg($in_file));

	$length = $end - $start;

	if ($length <= 0) return false;

	$stime = escapeshellarg(sprintf("%d:%02f", (int)($start / 60), ($start % 60)));
	$etime = escapeshellarg(sprintf("%d:%02f", (int)($end / 60), ($end % 60)));
	$ltime = escapeshellarg(sprintf("%d:%02f", (int)($length / 60), ($length % 60)));

	$clin_file = escapeshellarg($in_file);
	$clout_file = escapeshellarg($out_file);
	exec ('sox -t sox "|sox '.$clin_file.' -p trim 0 '.$stime.' pad 0 '.$ltime.'" -t sox "|sox '.$clin_file.' -p trim '.$etime.'" '.$clout_file, $trim_out, $trim_result);

	if ($trim_result == 0) {
		if ($empt_out) {
			unlink($in_file);
			rename($out_file, $in_file);
		} elseif ($unlink) unlink($in_file);
		
		return true;
	} else {
		return false;
	}
}

function fade($in_file, $fade_in, $fade_out = 0, $out_file = '', $unlink = false) {
	$empt_out = false;

	if (empty($out_file)) {
		$empt_out = true;
		$out_file = tempnam("/tmp/", basename($in_file, ".wav"));
		rename($out_file, $out_file.".wav");
		$out_file .= ".wav";
	}

	$file_len = exec("soxi -D ".escapeshellarg($in_file));
	$itime = escapeshellarg(sprintf("%d:%02f", (int)($fade_in / 60), ($fade_in % 60)));
	$otime = escapeshellarg(sprintf("%d:%02f", (int)($fade_out / 60), ($fade_out % 60)));
	$ltime = escapeshellarg(sprintf("%d:%02f", (int)($file_len / 60), ($file_len % 60)));

	exec ('sox '.escapeshellarg($in_file).' '.escapeshellarg($out_file).' gain -en fade h '.$itime.' '.$ltime.' '.$otime, $trim_out, $trim_result);

	if ($trim_result == 0) {
		if ($empt_out) {
			unlink($in_file);
			rename($out_file, $in_file);
		} elseif ($unlink) unlink($in_file);
		
		return true;
	} else {
		return false;
	}
}

if (!empty($_POST) && !empty($_POST['segments'])) {
	$segments = json_decode($_POST['segments'], true);
	if (!empty($segments['filename'])) {
		$filename = pathinfo($segments['filename'], PATHINFO_BASENAME);
		$path = __DIR__ . "/audio/" . $filename;
		$tmp_path = "/tmp/".uniqid(true).".wav";
		$out_path = __DIR__ . "/audio/" . pathinfo($segments['filename'], PATHINFO_FILENAME)."_".uniqid(true).".mp3";

		if (file_exists($path) && !file_exists($out_path)) {
			if (decode_mp3($path, $tmp_path)) {
				$file_len = exec("soxi -D ".escapeshellarg($tmp_path));

				$fade_in = $segments['fade_in'];
				$fade_out = $segments['fade_out'];
				$trim = $segments['trim'];
				$silence = $segments['silence'];
				$slow = $segments['slow'];
				$fast = $segments['fast'];

				foreach ($silence as $sil) {
					silence($tmp_path, $sil['start'], $sil['end']);
				}

				foreach ($slow as $slo) {
					change_rate($tmp_path, 0.5, $slo['start'], $slo['end']);
				}

				foreach ($fast as $fas) {
					change_rate($tmp_path, 2, $fas['start'], $fas['end']);
				}

				usort($trim, function($a, $b) { return $a['start'] == $b['start'] ? 0 : ($a['start'] < $b['start'] ? -1 : 1); });
				$last_trim_start = $last_trim_end = $file_len;

				foreach ($trim as $tri) {
					$end = $tri['end'];

					if ($tri['end'] > $last_trim_start) {
						if ($tri['end'] > $last_trim_end) {
							$end -= ($last_trim_end - $last_trim_start);
						} else {
							$end = $last_trim_start;
						}
					}

					trim_file($tmp_path, $tri['start'], $tri['end']);
					$last_trim_start = $tri['start'];
					$last_trim_end = $tri['end'];
				}

				fade($tmp_path, $fade_in[0]['end'], $fade_out[0]['start']);

				if (encode_mp3($tmp_path, $out_path, true)) {
					list($file_name, $etc) = explode("-", basename($out_path));
					ob_end_clean();
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.$file_name.".mp3");
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($out_path));
					readfile($out_path);
					die();
				}
			}
		}
	}
}
die('Unable to save edited file.');