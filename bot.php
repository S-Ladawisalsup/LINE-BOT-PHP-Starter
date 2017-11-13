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
								'text' => AnswerBuilder('ans')
							];
							break;	
						case '2':
							# code... When Question => Timer Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder('ans')
							];
							break;
						case '3':
							# code... Where Question => Location Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder('ans')
							];
							break;
						case '4':
							# code... Who Question => Personal Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder('ans')
							];
							break;
						case '5':
							# code... What/How Question => Reason Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder('ans')
							];
							break;
						case '6':
							# code... Which Question => Object Answer
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder('ans')
							];
							break;
						case '7':
							# Number Question (How + ...) => Number Answer
							if (GetQuesion($text, 'math')) {
								$mathematics = file('text/math.txt');
								foreach ($mathematics as $math) {
									$math = substr($math, 0, strlen($math) - 1);
									if (strpos($text, $math)) {
										$operator = $math;
										break;
									}
									else {
										$operator = 'null';
									}
								}

								if ($operator != 'null') {
									preg_match_all('!\d+\.*\d*!', $text, $matches);
									$val = $matches[0];

									if ((count($val) == 1) && GetQuesion($operator, 'issqrt')) {
										$solve = maths($val[0], 0, $operator);
									}
									else if (count($val) == 2) {
										$solve = maths($val[0], $val[1], $operator);
									}							 
								}

								if (isset($solve) === false) {
									$messages = [						
										'type' => 'text',
										'text' => AnswerBuilder('ans')
									];	
								}
								else {
									$messages = [						
										'type' => 'text',
										'text' => $solve . " จ้า"
									];	
								}
							}
							else {
								$messages = [						
									'type' => 'text',
									'text' => AnswerBuilder('ans')
								];							
							}
							break;	
						case '8':
							# ping mode
								$messages = [						
									'type' => 'text',
									'text' => 'ไป ping เองสิจ๊ะ'
								];
							break;				
						default:
							# Other cases not question
							if (GetQuesion($text, 'greeting')) {
								$day = strtolower(date("D"));
								$messages = [
									'type' => 'image',
								    'originalContentUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/' . $day . '_original.jpg',
								    'previewImageUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/' . $day . '_240.jpg'
								];
							}
							else {
								// Build message to reply back
								$messages = [						
									'type' => 'text',
									'text' => AnswerBuilder('res')
								];	
							}  
							break;
					}
				}			
			}

			# Check event user request is sticker
			else if ($event['message']['type'] == 'sticker') {
				// Get random sticker default by LINE Corp.
				$sticker = GetSticker();

				// Build message to reply back
				$messages = [
					'type' => 'sticker',
					'packageId' => $sticker['packageId'],
    				'stickerId' => $sticker['stickerId']
				];
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

echo "OK";