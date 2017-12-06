<?php
/************************************************************************************************************************************/
/*** include file(s) ***/
include 'utilities.php';
include 'registre.php';
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

		InsertIdToDB($event['source'][$event['source']['type'] . 'Id']);
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message') {
	
			# Check event user request is text
			if ($event['message']['type'] == 'text') {
				$bot_mod = IsAvailable($event['source'][$event['source']['type'] . 'Id']);
				switch ($bot_mod) {
					case 'regis':
						if (startsWith($haystack, $bot_name) || $event['source']['type'] == 'user') {
							if ($event['source']['type'] != 'user') {
								$text = substr($event['message']['text'], strlen($bot_name));
							}
							else {
								$text = $event['message']['text'];
							}
							$messages = [						
								'type' => 'text',
								'text' => RegisterMode($text, $event['source'][$event['source']['type'] . 'Id'], $event['source']['type'])
							];
						}
						break;
					case 'allow':
						// Compare message calling bot's name
						$haystack = strtolower($event['message']['text']);
						if (startsWith($haystack, $bot_name) || $event['source']['type'] == 'user') {

							// Get text echo without bot's name
							if ($event['source']['type'] != 'user') {
								$text = substr($event['message']['text'], strlen($bot_name));
							}
							else {
								$text = $event['message']['text'];
							}
							$text = SubEndText($text);

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
									if (strpos($text, 'testmsgbyball') !== false) {
										//InsertDataToDB($event['source'][$event['source']['type'] . 'Id'], $event['source']['type']);
										$messages = [						
											'type' => 'text',
											'text' => 'xxx'
										];
									}
									//--------------------------------------------------------
									else if ((strpos($text, 'เปิดโหมดลงทะเบียนเข้าใช้งาน') !== false)) {
										$messages = [						
											'type' => 'text',
											'text' => "คุณสามารถใช้งาน Line Chat Bot ได้อย่างเต็มรูปแบบแล้วจ้า"
										]; 
									}
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
						break;
					case 'await':
						$text = $event['message']['text'];
						if (((strpos($text, 'ไม่') !== false) || (strpos(strtolower($text), 'no') !== false) || 
							 (strpos($text, 'ยกเลิก') !== false) || (strpos(strtolower($text), 'cancel') !== false) || 
							 (strpos($text, 'ปฏิเสธ') !== false) || (strpos(strtolower($text), 'refuse') !== false))) {
							$messages = [						
								'type' => 'text',
								'text' => DeleteIdRow($text)
							];
							if (ListWaitRegister() == "ไม่มีรายชื่อขอเข้าใช้งานเต็มรูปแบบตกค้าง") {
								ReturnAllowToAdmin();
							}
						}
						else if ((strpos($text, 'ใช่') !== false) || (strpos(strtolower($text), 'yes') !== false) || 
							 	 (strpos($text, 'ตกลง') !== false) || (strpos(strtolower($text), 'accept') !== false) || 
							 	 (strpos($text, 'ยอมรับ') !== false) || (strpos(strtolower($text), 'confirm') !== false) || 
							 	 (strpos($text, 'ยืนยัน') !== false) || (strpos(strtolower($text), 'yeah') !== false) || 
							 	 (strpos($text, 'ชัวร์') !== false) || (strpos(strtolower($text), 'sure') !== false) || 
							 	 (strpos($text, 'แน่นอน') !== false) || (strpos(strtolower($text), 'absolute') !== false) || 
							 	 (strpos($text, 'คอนเฟิร์ม') !== false) || (strpos($text, 'อนุมัติ') !== false) || 
							 	 (strpos($text, 'โอเค') !== false) || (strpos(strtolower($text), 'ok') !== false)) {
							$messages = [						
								'type' => 'text',
								'text' => ConfirmRowUserMember($text)
							];	
							if (ListWaitRegister() == "ไม่มีรายชื่อขอเข้าใช้งานเต็มรูปแบบตกค้าง") {
								ReturnAllowToAdmin();
							}
						}
						else if (findQuestionType($text) == 4 && strpos($text, 'เหลือ') !== false) {
							$messages = [						
								'type' => 'text',
								'text' => ListWaitRegister()
							];	
						}
						else {
							$messages = [						
								'type' => 'text',
								'text' => 'ทำเป็นเล่นอยู่นั่น ตอบมาอนุมัติมั้ย'
							];	
						}
						break;		
					default:
						# Check event user request is text
						if ($event['message']['type'] == 'text') {

							// Compare message calling bot's name
							$haystack = strtolower($event['message']['text']);
							if (startsWith($haystack, $bot_name) || $event['source']['type'] == 'user') {

								// Get text echo without bot's name
								if ($event['source']['type'] != 'user') {
									$text = substr($event['message']['text'], strlen($bot_name));
								}
								else {
									$text = $event['message']['text'];
								}
								$text = SubEndText($text);

								// Check text is question
								$typing = findQuestionType($text);
								if ($typing > 0 && $typing < 7) {
									$messages = [						
										'type' => 'text',
										'text' => AnswerBuilder(10)
									];		
								}
								else if ($typing == 7) {
									if (strpos($text, 'อุณหภูมิ') !== false) {							
										$messages = [						
											'type' => 'text',
											'text' => 'อยากรู้ก็เดินไปวัดเองสิจ๊ะ'
										];	
									}
									else {
										$messages = [						
											'type' => 'text',
											'text' => AnswerBuilder(10)
										];							
									}		
								}
								else if ($typing == 8) {
									$messages = [						
										'type' => 'text',
										'text' => 'อยากรู้ก็ไป ping เองสิจ๊ะ'
									];		
								}
								else if ((strpos($text, 'เปิดโหมดลงทะเบียนเข้าใช้งาน') !== false)) {
									SetRegisterSeq($event['source'][$event['source']['type'] . 'Id']);
									if ($event['source']['type'] == 'user') {
										$tx = "กรุณาระบุชื่อที่ให้ใช้เรียก (ชื่อเล่นก็ได้นะ)";
									}
									else if ($event['source']['type'] == 'group') {
										$tx = "กรุณาระบุชื่อกลุ่ม";
									}
									else if ($event['source']['type'] == 'room') {
										$tx = "กรุณาระบุชื่อห้อง";
									}
									else {
										$tx = "ขออภัยขณะนี้ระบบลงทะเบียนมีปัญหา ไว้มาลงทะเบียนใหม่ทีหลังน๊ะจ๊ะคนดีดนเก่งของพี่จุ๊บๆ 
											   หรือลองไปติดต่อ ITSD ดูน๊ะจ๊ะ";
									}
									$messages = [						
										'type' => 'text',
										'text' => $tx
									]; 
								}
								else {
									$messages = [						
										'type' => 'text',
										'text' => AnswerBuilder(13)
									];	 	
								}
							}			
						}
						break;	
				}
			}

			# Check event user request is sticker
			else if ($event['message']['type'] == 'sticker') {

				$rand_chance = rand(0, 10000);

				if ($rand_chance % 2 == 0) {
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

echo "OK";