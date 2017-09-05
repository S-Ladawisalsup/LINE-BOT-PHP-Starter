<?php

/************************************************************************************************************************************/
/*** PHP Function Zone. ***/
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

/************************************************************************************************************************************/


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

			if ($event['message']['type'] == 'text') {

				//Compare message calling bot's name
				$haystack = strtolower($event['message']['text']);
				if (startsWith($haystack, $bot_name)) {

					// Get text sent echo without bot's name
					$text = substr($event['message']['text'], strlen($bot_name));

					if (endsWith($text, 'หรือไม่') || endsWith($text, 'ไหม') || endsWith($text, 'มั้ย') || endsWith($text, 'เท่าไร') || endsWith($text, 'เท่าไหร่') || endsWith($text, '?')) {
						$messages = [						
							'type' => 'text',
							'text' => 'ไม่รู้จ้า'
						];							
					}
					else if (strpos($text, 'image')) {

						'type': 'image',
    					'originalContentUrl': 'http://mumraisin.com/wp-content/uploads/2017/08/1-za-790-1024x1024.jpg',
    					'previewImageUrl': 'http://mumraisin.com/wp-content/uploads/2017/08/1-za-790-1024x1024.jpg'

					}
					else {
						// Build message to reply back
						$messages = [						
							'type' => 'text',
							'text' => $text . 'จ้า'
						];	
					}
				}			
			}
			else if ($event['message']['type'] == 'sticker') {
				// Get random number of sticker
				$var = rand(1,20);

				// Build message to reply back
				$messages = [
					'type' => 'sticker',
					'packageId' => '1',
	    			'stickerId' => $var
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