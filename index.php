<?php
    # show error message
    ini_set('error_reporting', 1);
    ini_set('display_errors', 1);
    #require the required files
	require './Telegram/TelegramBot.php';
	require './db_config/db_config.php';
	require './helpers/functions.php';
    #require Telegram Bot func | It helps to response to user's updates
	use TelegramBot as Bot;
	$bot = new Bot(['botToken'=>"6759095381:AAFNQ_PhALW8fsqi-rOoSfpOI4cxjMmPPV0"]); # set up bot token as array and send it as argument

    require_once './helpers/variebles.php';
	$db = new db_config;
    #include second parameters
	include './helpers/sendMessageToUsers.php';
	include './helpers/activeUsers.php';
    include './helpers/markups.php';


    #handle the updates
	if ($update) {
        #get message updated
		if (isset($update->message)) {
			if ($type == 'private') {
                #insert user to database
				if (removeBotUserName($text) == "/start") {
					$myUser = myUser(['fromid','name','user','chat_type','lang','del'],[$fromid,$full_name,$user ?? null,'private','',0]);
				}
                #inserting user to database has been finished

                #check if user is subscribed to all required channels
				if (channel($fromid)) {
                    #get user data from database
					$user = mysqli_fetch_assoc(
						$db->selectWhere('users',[
							[
								'fromid'=>$fromid,
								'cn'=>'='
							]
						])
					);
					$user_data = json_decode($user['data']);

                    #action starts from next line
                    if (removeBotUserName($text) == "/start") {
						$db->updateWhere('users',
							[
								'data'=>'region',
								'step'=>1
							],
							[
								'fromid'=>$fromid,
								'cn'=>'='
							]
						);
						$bot->sendChatAction('typing', $chat_id)->setInlineKeyBoard($regions)->sendMessage("<b>Assalomu alaykum, " . $full_name ." <b>ARGOS Namangan</b>ning rasmiy botiga xush kelibsiz. Viloyatingizni tanlang</b>");
						exit();
					}
				}
			}else{
				if (removeBotUserName($text) == "/start") {
					$myUser = myUser(['fromid','name','user','chat_type','lang','del'],[$fromid,$full_name,$user,'group','',0]);

					if ($myUser) {
						$bot->sendChatAction('typing', $chat_id)->sendMessage('Assalomu alaykum, xush kelibsiz!');
						exit();
					}
					$bot->sendChatAction('typing', $chat_id)->sendMessage('Assalomu alaykum, qayata xush kelibsiz!');
					exit();
				}
			}
		}
        #get callback query updates
        else if (isset($update->callback_query)) {
            #checking if user subscribed to all required channels
			if (channel($call_from_id)) {
                #get data from db about the user
                $user = mysqli_fetch_assoc(
                    $db->selectWhere('users',[
                        [
                            'fromid'=>$call_from_id,
                            'cn'=>'='
                        ]
                    ])
                );
                $user_data = json_decode($user['data']);

                # actions start from here
				if ($data) {
                    $bot->sendChatAction('typing', $call_from_id)->sendMessage("testing...");

                    if ($user['region']){
                        $db->updateWhere('users',
                            [
                                'data'=>'default',
                                'step'=>1,
                                'region'=>$data
                            ],
                            [
                                'fromid'=>$call_from_id,
                                'cn'=>'='
                            ]
                        );
                        $bot->sendChatAction('typing', $call_from_id)->sendMessage("O'z xabaringizni yuboring");
                        exit();
                    }
				}
			}
		}
	}

	include 'helpers/admin/admin.php';