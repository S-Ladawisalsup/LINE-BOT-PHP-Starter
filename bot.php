<?php

/************************************************************************************************************************************/
/*** PHP Function Zone. ***/

function MessageBuilder($messages){
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

	return $result;
}

/************************************************************************************************************************************/


$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);


// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message') {
			if ($event['message']['type'] == 'text') {
				// Get text sent
				$text = $event['message']['text'] . 'จ้า';

				// Build message to reply back
				$messages = [
					'type' => 'text',
					'text' => $text
				];				
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

			$result = MessageBuilder($messages);

			echo $result . "\r\n";	
		}
	}
}
echo "OK";