<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

$out = array();

if (!empty($_FILES)) {
	if (empty($_FILES['file_upload']['error'])) {
		$tempFile = $_FILES['file_upload']['tmp_name'];
		$targetPath = "audio/";
		$fileParts  = pathinfo($_FILES['file_upload']['name']);
		
		if ($fileParts['extension'] == "mp3" && $_FILES['file_upload']['type'] == 'audio/mp3') {
			$new_name = str_replace(array('.',' ','-'),'', $fileParts['filename'])."-";
			$new_name .= str_replace(array('.',' ','-'),'', uniqid(true));

			$targetFile = $targetPath . $new_name . "." . $fileParts['extension'];

			if (file_exists(__DIR__ . "/" . $targetFile)) {
				$out['error'] = "A file with that name already exists.";
			} else {
				if (move_uploaded_file($tempFile, __DIR__ . "/" . $targetFile)) {
					if (chmod(__DIR__ . "/" . $targetFile, 0644)) {
						$out['file_name'] = $targetFile;
					} else {
						$out['error'] = "Unable to set file permissions.";
					}
				} else {
					$out['error'] = "Unable to save file.";
				}
			}
		} else {
			$out['error'] = "Invalid file type.";
		}
	} else {
		$out['error'] = "There was an error uploading the file.";
	}
} else {
	$out['error'] = "No file uploaded.";
}

echo json_encode($out);

die();