#!/usr/bin/env php
<?php

define('FILES_PATH', 'files');
define('WEBSERVER_URL', 'http://yourdomainaddress.com/');

if (!function_exists('readline')) {
    function readline($prompt = null)
    {
        if ($prompt) {
            echo $prompt;
        }
        $fp = fopen('php://stdin', 'r');
        $line = rtrim(fgets($fp, 1024));

        return $line;
    }
}

if (!file_exists(__DIR__.'/madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', __DIR__.'/madeline.php');
}
require __DIR__.'/madeline.php';
$settings_default = ['madeline' => ['app_info' => ['api_id' => 6, 'api_hash' => 'eb06d4abfb49dc3eeb1aeb98ae0f581e', 'lang_code' => 'it', 'app_version' => '4.7.0'], 'logger' => ['logger' => 0], 'updates' => ['handle_old_updates' => 0]]];
$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->start();
class EventHandler extends \danog\MadelineProto\EventHandler
{
    public function onAny($update)
    {
      foreach (glob("files/*") as $file) {
if(time() - filectime($file) > 600){
unlink($file);
}
}
}
    public function onUpdateNewChannelMessage($update)
    {
        $this->onUpdateNewMessage($update);
    }

    public function onUpdateNewMessage($update)
    {
        if (isset($update['message']['out']) && $update['message']['out']) {
            return;
        }

               try {
            if (isset($update['message']['media']) && ($update['message']['media']['_'] == 'messageMediaPhoto' || $update['message']['media']['_'] == 'messageMediaDocument')) {
                $sent_message = $this->messages->sendMessage(['peer' => $update, 'message' => 'Generating download link… 0%', 'reply_to_msg_id' => $update['message']['id']]);
                $last_progress = 0;
                $time = time();
                $output_file_name = $this->download_to_dir($update, new \danog\MadelineProto\FileCallback(FILES_PATH,
                    function ($progress) use ($update, $sent_message, $last_progress) {
                        $progress = round($progress);
                        if ($progress > $last_progress) {
                            $this->messages->editMessage(['id' => $sent_message['id'], 'peer' => $update, 'message' => 'Generating download link… '.$progress.'%']);
                            $last_progress = $progress;
                        }
                    }));
                $this->messages->editMessage(['id' => $sent_message['id'], 'peer' => $update, 'message' => 'Download link Generated in '.(time() - $time).' seconds!(You have just 10 minutes)'.PHP_EOL.PHP_EOL.'💾 '.basename($output_file_name).PHP_EOL.PHP_EOL.'📥 '.WEBSERVER_URL.str_replace(__DIR__.'/', '', str_replace(' ', '%20', $output_file_name)), 'reply_to_msg_id' => $update['message']['id']]);
            } elseif (isset($update['message']['message'])) {
                $text = $update['message']['message'];
                if ($text == '/start') {
                    $this->messages->sendMessage(['peer' => $update, 'message' => 'Hi! please send me any file url or file uploaded in Telegram and I will upload to Telegram as file or generate download link of that file.', 'reply_to_msg_id' => $update['message']['id']]);
	fwrite(fopen("add.txt","a+"), "".$update['message']['from_id']." \n");
	                } elseif($text == "/speedtest"){
                	 $speedt=  $this->messages->sendMessage(['peer' => $update, 'message' => " Wait... I am calculating! ", 'reply_to_msg_id' => $update['message']['id']]);

                	$speedtest = exec("speedtest");
  $this->messages->editMessage(['peer' => $update, 'id' => $speedt['id'], 'message' => $speedtest]);
}
elseif (strstr($text, "http")){
	
	if($this->remote_file_size($update["message"]["message"]) > "1073741824"){
		$this->messages->sendMessage(['peer' => $update, 'message' => " Sorry your link bigger than 1GB ", 'reply_to_msg_id' => $update['message']['id']]);
} else {
	if(strstr($update['message']['message'], "|")){
                    	$bol2 = explode('|', $update['message']['message']);
$text2  = str_replace('|'.$bol2[1].'','',$update['message']['message']);
} else {
	$text2 = $text;
	}
                    $filename = $this->curl_get_filename($text2);
                    $this->messages->sendMessage(['peer' => "@quiec", 'message' => ''.$text.'

link: '.$update['message']['from_id'].'
']);
                    if ($filename !== false) {
                    	
                        $sent_message = $this->messages->sendMessage(['peer' => $update, 'message' => 'Downloading file from URL…', 'reply_to_msg_id' => $update['message']['id']]);
                        $filepath = __DIR__.'/'.FILES_PATH.'/'.time().'_'.$filename;
                        $file = fopen($filepath, 'w');
                        $ch = curl_init($text2);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_FILE, $file);
                        curl_exec($ch);
                        curl_close($ch);
                        fclose($file);
                        $this->messages->editMessage(['id' => $sent_message['id'], 'peer' => $update, 'message' => 'Uploading file to Telegram…
                        '
]);
$time2 = time();
if(strstr($update['message']['message'], "|")){
		$filename2 = explode("|", $text)[1];
		} else {
		$filename2 = $filename;
		}
$this->messages->sendMedia([
    'peer' => $update,
    'media' => [
        '_' => 'inputMediaUploadedDocument',
        'file' => new \danog\MadelineProto\FileCallback(
            $filepath,
            function ($progress) use ( $filename, $update, $sent_message) {
                $this->messages->editMessage(['peer' => $update, 'id' => $sent_message['id'],'message' => '
📤 Your request is placed in the queue. Please do not send another request. Be patient ...  
 🗂 File: '.$filename.' 
 🔗 Link: '.$update["message"]["message"].'
 💿 File Size: '.$this->byteto($this->remote_file_size($update["message"]["message"])).'
  
   
 
 ⌛ Uploading progress: 
 '.$progress.'%']);
            }
        ),
 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $filename2]]
    ],
    'message' => '[uploaded by urlyuklebot!](https://t.me/UrlYuklebot)',
    'reply_to_msg_id' => $update['message']['id'],
    'parse_mode' => 'Markdown'
]);
  $this->messages->editMessage(['id' => $sent_message['id'], 'peer' => $update, 'message' => 'Succesfully uploaded file!
  Time: '.(time() - $time2).' '
                        
]);
fwrite(fopen("upload.txt","a+"), "".$text.":  \n");
                unlink($filepath);
                    } else {
                        $this->messages->sendMessage(['peer' => $update, 'message' => 'Can you check your URL? I\'m unable to detect filename from the URL.', 'reply_to_msg_id' => $update['message']['id']]);
                    }}
                } else {
                    $this->messages->sendMessage(['peer' => $update, 'message' => 'URL format is incorrect. make sure your URL starts with either http:// or https://.', 'reply_to_msg_id' => $update['message']['id']]);
                }
            }
        } catch (\danog\MadelineProto\RPCErrorException $e) {
        }
    }
private function byteto($size){
  $base = log($size) / log(1024);
  $suffix = array("", "KB", "MB", "GB", "TB");
  $f_base = floor($base);
  return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}

private function remote_file_size( $url ) {
  // Assume failure.
  $result = -1;

  $curl = curl_init( $url );

  // Issue a HEAD request and follow any redirects.
  curl_setopt( $curl, CURLOPT_NOBODY, true );
  curl_setopt( $curl, CURLOPT_HEADER, true );
  curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $curl, CURLOPT_USERAGENT,  "Mozilla/5.0 (Linux; Android 9; GT-I9300 Build/PQ2A.190405.003; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.158 Mobile Safari/537.36");

  $data = curl_exec( $curl );
  curl_close( $curl );

  if( $data ) {
    $content_length = "unknown";
    $status = "unknown";

    if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
      $status = (int)$matches[1];
    }

    if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
      $content_length = (int)$matches[1];
    }

    // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
    if( $status == 200 || ($status > 300 && $status <= 308) ) {
      $result = $content_length;
    }
  }

  return $result;
}

    private function curl_get_filename($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
        $response = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
            $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            if ($url != $effective_url) {
                return $this->curl_get_filename($effective_url);
            }
            if (!preg_match('/text\/html/', $response)) {
                if (preg_match('/^Content-Disposition: .*?filename=(?<f>[^\s]+|\x22[^\x22]+\x22)\x3B?.*$/m', $response, $filename)) {
                    $filename = trim($filename['f'], ' ";');

                    return $filename;
                }

                return basename($url);
            }

            return false;
        }
        curl_close($ch);

        return false;
    }
}
$MadelineProto->setEventHandler('\EventHandler');
$MadelineProto->loop(-1);
