<?php
/*
	error_reporting(E_ALL);

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
*/
	if(!isset($_GET['img']) || !$_GET['img']) {
		sendFailImage('No image given.');
		die();
	}
	$stamp = imagecreatefrompng('./stamps/play_button.png');

	if(!$stamp) {
		sendFailImage('Invalid stamp icon.');
		die();
	}

	$baseImgSource = $_GET['img'];

	$baseImgSource = urldecode($baseImgSource);

	$mimeCode = exif_imagetype($baseImgSource);

	switch ($mimeCode) {
		case IMAGETYPE_JPEG:
			$baseImg = imagecreatefromjpeg($baseImgSource);
			break;
		case IMAGETYPE_PNG:
			$baseImg = imagecreatefrompng($baseImgSource);
			break;
		case IMAGETYPE_GIF:
			$baseImg = imagecreatefromgif($baseImgSource);
			break;
		default:
			sendFailImage('This is not an accepted image type. Accepted types are jpg/jpeg, png and gif');
			die();
			break;
	}

	if(!$baseImg) {
		sendFailImage('Invalid image given.');
		die();
	}

	$baseImgWidth = imagesx($baseImg);
	$baseImgHeight = imagesy($baseImg);

	$stampHeight = imagesy($stamp);
	$stampWidth = imagesx($stamp);

	if($baseImgWidth < $stampWidth || $baseImgHeight < $stampHeight) {
		sendFailImage('The image is smaller then the stamp icon.');
		die();
	}

	$stampX = floor(($baseImgWidth - $stampWidth) / 2);
	$stampY = floor(($baseImgHeight - $stampHeight) / 2);

	if(!imagecopy($baseImg, $stamp, $stampX, $stampY, 0, 0, $stampWidth, $stampHeight)) {
		sendFailImage('Could not stamp the image.');
		die();
	};

	header('Content-type: image/png');
	imagepng($baseImg);
	imagedestroy($baseImg);

	function sendFailImage($message) {
		$failImage = imagecreate(200, 200);
		imagecolorallocate($failImage, 220, 220, 220);
		$text_color = imagecolorallocate($failImage, 233, 14, 91);
		$textX = 10;
		$textY = 10;
		$lineHeight = 20;

		$wrappedMessage = wordwrap($message, 20, '|');
		$messageArray = explode('|', $wrappedMessage);
		for($i = 0; $i < count($messageArray); $i++) {
			imagestring($failImage, 3, $textX, $textY, $messageArray[$i], $text_color);
			$textY += $lineHeight;
		}
	
		header('Content-type: image/png');
		imagepng($failImage);
		imagedestroy($failImage);	
	}
?>