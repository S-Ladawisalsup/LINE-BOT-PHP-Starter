<?php
/************************************************************************************************************************************/
/*** include file(s) ***/
include 'utilities.php';
/************************************************************************************************************************************/
date_default_timezone_set("Asia/Bangkok");
$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

$bot_name = '@kiki';

// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message') {

			# Check event user request is text
			if ($event['message']['type'] == 'text') {

				// Compare message calling bot's name
				$haystack = strtolower($event['message']['text']);
				if (startsWith($haystack, $bot_name)) {

					// Get text echo without bot's name
					$text = substr($event['message']['text'], strlen($bot_name));

					// Check text is question
					$typing = findQuestionType($text);
					switch ($typing) {
						case '1':
							# code... Yes/No Question => Yes/No Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder(10)
							];							
							break;	
						case '2':
							# code... When Question => Timer Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder(12)
							];
							break;
						case '3':
							# code... Where Question => Location Answer
							$locate = GetLocation();
							if ($locate != null) {
								$messages = [						
									'type' => 'location',
									'title' => $locate['title'],
									'address' => $locate['address'],
									'latitude' => $locate['latitude'],
									'longitude' => $locate['longitude']
								];
							}
							else {
								$messages = [						
									'type' => 'text',
									'text' => AnswerBuilder(10)
								];
							}
							break;
						case '4':
							# code... Who Question => Personal Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder(11)
							];
							break;
						case '5':
							# code... What/How Question => Reason Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder(10)
							];							
							break;
						case '6':
							# code... Which Question => Object Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder(10)
							];
							break;
						case '7':
							# Number Question (How + ...) => Number Answer
							if (strpos($text, 'อุณหภูมิ') !== false) {							
								$messages = [						
									'type' => 'text',
									'text' => GetTemperature($text)
								];	
							}
							else {
								$messages = [						
									'type' => 'text',
									'text' => AnswerBuilder(10)
								];							
							}
							break;	
						case '8':
							# ping mode
							$protocal = IsAskedServer($text);
							if ($protocal['IsChecked']) {
								$messages = [						
									'type' => 'text',
									'text' => GetPingAnswer($protocal['ip_addr'])
								];	
							}
							else {
								$messages = [						
									'type' => 'text',
									'text' => 'ไม่มีข้อมูลในระบบจ้า อยากรู้ก็ไป ping เองสิจ๊ะ'
								];	
							}
							break;		
						case '9':
							# greeting mode
							$day = strtolower(date("D"));
							$messages = [
								'type' => 'image',
							    'originalContentUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/' . $day . '_original.jpg',
							    'previewImageUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/' . $day . '_240.jpg'
							];
							break;		
						default:
							//--------------------------------------------------------
							// Test case to insert data to postgresql database.
							if (strpos($text, 'ที่นี้โรงพยาบาลชั้นนำ') !== false) {
								//InsertDataToDB();
								$messages = [						
									'type' => 'text',
									'text' => '555'
								];
							}
							//--------------------------------------------------------
							else {
								// Build message to reply back
								$messages = [						
									'type' => 'text',
									'text' => AnswerBuilder(13)
								];	
							}  
							break;
					}
				}			
			}

			# Check event user request is sticker
			else if ($event['message']['type'] == 'sticker') {

				$rand_chance = rand(0, 1);

				if ($rand_chance == 0) {
					// Get random sticker default by LINE Corp.
					$sticker = GetSticker();

					// Build message to reply back
					$messages = [
						'type' => 'sticker',
						'packageId' => $sticker['packageId'],
	    				'stickerId' => $sticker['stickerId']
					];
				}
			}

			// Get replyToken
			$replyToken = $event['replyToken'];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			echo $result . "\r\n";
		}
	}
}

if ((date('m') % 5 == 0) && (date('s') % 60 == 0)) {
	$messages = [						
		'type' => 'text',
		'text' => 'อยากอกหัก...แต่อุปสรรคดันอยู่ที่หน้าตา เห้อออ'
	];
	$nonnoi = 'Ca35db1d5c584c6467d717df89a0302ec';
	// Make a POST Request to Messaging API to push to sender
	$url = 'https://api.line.me/v2/bot/message/push';
	$data = [
		'to' => $nonnoi,
		'messages' => [$messages],
	];
	$post = json_encode($data);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	curl_close($ch);

	echo $result . "\r\n";
	sleep(2);
}

echo "OK";