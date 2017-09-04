<?php
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
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			$text = $event['message']['text'] . ' จ้า';
			// Get replyToken
			$replyToken = $event['replyToken'];

			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => $text
			];

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
		else if ($event['type'] == 'message' && $event['message']['type'] == 'sticker') {
			$dayofweek = date('l');

			switch (dayofweek) {
				case 'Sunday':
					$img_url = "http://สวัสดีตอนเช้า.com/wp-content/uploads/2017/04/2460-1-326x326.png";
					break;
				case 'Monday':
					$img_url = "http://สวัสดีตอนเช้า.com/wp-content/uploads/2017/06/12660-326x232.png";
					break;
				case 'Tueday':
					$img_url = "http://สวัสดีตอนเช้า.com/wp-content/uploads/2017/07/18760-326x214.png";
					break;
				case 'Wednesday':
					$img_url = "http://สวัสดีตอนเช้า.com/wp-content/uploads/2017/05/31560-326x271.png";
					break;
				case 'Thursday':
					$img_url = "http://สวัสดีตอนเช้า.com/wp-content/uploads/2017/07/13760-326x326.png";
					break;
				case 'Friday':
					$img_url = "http://สวัสดีตอนเช้า.com/wp-content/uploads/2017/05/26560-326x245.png";
					break;
				case 'Saturday':
					$img_url = "http://สวัสดีตอนเช้า.com/wp-content/uploads/2017/06/3660-326x311.png";
					break;				
				default:
					# code...
					break;
			}

			// Get text sent
			// $text = 'อย่าส่งสติ๊กเกอร์มาจิ เค้าไม่มีนะตัวเอง ส่งเป็นของขวัญมาให้เค้าหน่อยได้ป๊ะล่ะ';
			// Get replyToken
			$replyToken = $event['replyToken'];

			// Build message to reply back
			$messages = [
				'type' => MessageType::IMAGE,
				'originalContentUrl' => $this->img_url,
                'previewImageUrl' => $this->img_url
			];

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