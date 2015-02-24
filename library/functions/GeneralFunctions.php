<?php

function _pr($obj, $exit = 0) {
    echo "<pre>";
    print_r($obj);
    echo "</pre>";
    if ($exit == 1) {
    	exit();
    }
}


/**
 * Check given date belongs to current week 
 *  
 * */
function date_belongsto_currentweek($date=null){
    return ( strtotime($date) >= strtotime('monday this week') && strtotime($date) <= strtotime('sunday this week') );
}

/**
 * Return formatted error message
 * 
 * 
 * @params  message  
 *  
 * */
function formatErrorMessage($varMessage)
{	//_pr($varMessage,1);
    $messageErr="<ul>";
    if (is_array($varMessage)) {
        foreach ($varMessage as $message) {
        	if(is_array($message)){
            	foreach ($message as $key => $val)
            	$messageErr .= "<li>".$val."</li>";
        	}else {
        		$messageErr .= "<li>".$message."</li>";
        	}
        }

    } else {
        $messageErr .= "<li>".$varMessage."</li>";
    }
    $messageErr .="</ul>";
    return $messageErr;
}

function escapeString($string) {
    $tmpString = str_replace("&nbsp;","#@#",$string);
    $tmpString = htmlspecialchars($tmpString, ENT_COMPAT, "ISO-8859-1");
    $tmpString = str_replace("#@#","&nbsp;",$tmpString);
    return $tmpString;
}

/**
 * Return File extension 
 * 
 * 
 * @params  $filename file name   
 *  
 * */

function findExts($filename)
{	
	preg_match("/\.([^\.]+)$/", strtolower($filename) ,  $matches); 
	return $matches[1]; 
}    

/**
 * Return All files in the directory 
 * 
 * 
 * @params  $path Direcotry path  
 *  
 **/
function getFilesFromDir($path)
{	
	if(is_dir($path))
	{
		if ($handle = opendir($path)) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				if ($file != "." && $file != ".." && $file != ".svn") 
				{
					if(in_array(findExts($file),  explode( ',' , DATAFILE_CRON_FILE_TYPE )))
					{
						$arrTemp[] = $file;
					}	
				}
			}
			closedir($handle);
		}
	}
	return (isset($arrTemp)) ? $arrTemp : array() ;
}	

/**
 * Return FTP connectoin 
 * @params  $host for Ftp host name or ip 
 * @params  $username for Ftp username
 * @params  $password for Ftp password 
 * @params  $port for Ftp port 
 * @params  $is_secure for SFtp OR Ftp  
 * 
 **/
function getFTPConnection($host,$username,$password,$port=21,$is_secure='No')
{	
    $contents = new Zend_Ftp($host, $username,$password,$port);
    $contents->setSecure(($is_secure == 'Yes') ? true : false);
    $contents->setPassive(true);
    
    return $contents;
}	


/**
 * Return All files in the directory 
 * 
 * @params  $conn for FTP Connection onbject
  * */
function getFilesFromDirFTP( $conn )
{	
		$dir = $conn->getCurrentDirectory();
		$contents = (isset($dir)) ? $dir->getContents() : array() ;

		//_pr($contents);
		//var_dump($conn->isConnected());
		

		if(!$conn->isConnected())
		{
			$i=0;
			foreach ($contents as $content) 
			{ 
				if ($content->isFile()) 
				  { 
					if(in_array(findExts($content->name),  explode( ',' , DATAFILE_CRON_FILE_TYPE )))
					{
						$arrRFtpFiles[$i] = $content->name ; 
					}	
				  }
				  $i++;
			}
		}	
		
	 return (isset($arrRFtpFiles)) ? $arrRFtpFiles : array() ;
}	
/**
 * Return All fields from provided file
 * 
 * @params  $providerMapNs for provider object
 * @params  $file_name for file name
 * @return array fields
 * */
function getFieldsFromfile( $providerMapNs , $file_name)
{	
	if($file_name != '')
	{
		
		switch ( $providerMapNs->file_upload_type )
		{
			case 'remote_ftp' :	
			case 'ftp' :
				$fo = fopen('ftp://'.$providerMapNs->username.':'.$providerMapNs->password.'@'.$providerMapNs->server_name.'/'.$file_name,'rd');
			break;
			default :
				$fo = fopen(DATAFILE_ROOT_PATH.$providerMapNs->id."/".PROVIDER_INVENTORY."/".$file_name ,'rd' );
		}	
	
		$file_content = fgetcsv($fo);
	}	
	
	return (isset($file_content)) ? $file_content : array() ;
}

/**
 * Extract a substring of a given string between two substrings
 * @param string $str input string
 * @param string $start starting token
 * @param string $end ending token
 * @return string cleaned string
 */
function extract_string($str, $start, $end) 
{
	$str_low = strtolower($str);
	$pos_start = strpos($str_low, $start);
	if ($pos_start === FALSE) {
		return FALSE;
	}
	$pos_end = strpos($str_low, $end, ($pos_start + strlen($start)));
	if ($pos_end === FALSE) {
		return FALSE;
	}
	if ( ($pos_start !== false) && ($pos_end !== false) ) {
		$pos1 = $pos_start;
		$pos2 = $pos_end;
		return substr($str, $pos1, $pos2);
	}
}

//Get the text between $start and $end
function GetBetween($content,$start,$end)
{
	$r = explode($start, $content);
   
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $start.$r[0].$end;
    }
    return '';
}

//Split text up into 140 char array for Twitter
function split_to_chunks($to,$text){
	$total_length = (140 - strlen($to));
	$text_arr = explode(" ",$text);
	$i=0;
	$message[0]="";
	foreach ($text_arr as $word){
		if ( strlen($message[$i] . $word . ' ') <= $total_length ){
			if ($text_arr[count($text_arr)-1] == $word){
				$message[$i] .= $word;
			} else {
				$message[$i] .= $word . ' ';
			}
		} else {
			$i++;
			if ($text_arr[count($text_arr)-1] == $word){
				$message[$i] = $word;
			} else {
				$message[$i] = $word . ' ';
			}
		}
	}
	return $message;
}

//Extract emails from a string
function extract_emails($str){
    // This regular expression extracts all emails from a string:
    $regexp = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
    preg_match_all($regexp, $str, $m);

    return isset($m[0]) ? $m[0] : array();
}

function sendMSG($msg='msg',$subject='(none)'){
	if($msg!=''){
		$mail = new PHPMailer(); // defaults to using php "mail()"
		$mail->From       = "tejasvyaas@gmail.com";
		$mail->FromName   = "Cron Job MSG";
		$mail->Subject    = $subject;
		$mail->IsHTML(false); 
		$mail->AltBody = $msg;
		$mail->MsgHTML($msg);
		$mail->AddAddress("tejasvyaas@gmail.com", "Teja Vyas");
		$mail->Send();
	}
}


function getPDOConn()
{
	global $configuration;
	
	$host = $configuration->database->params->db3->host	;
	$dbname = $configuration->database->params->db3->dbname	;
	$username = $configuration->database->params->db3->username	;
	$password = $configuration->database->params->db3->password	;
	
	$pdo = new PDO(
				'mysql:host='.$host.';dbname='.$dbname,
				 $username , $password,
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		   );

	
	return $pdo;		
}

//validates a phone number, requiring area code, otherwise lenient on -,(,' ' formatting
function validatePhone($phone) {
	$trimmed = str_replace(' ', '', trim($phone));
	if (preg_match(USA_PHONE_NUMBER_REGEX, $trimmed, $match) and strlen(trim($match['area'])) > 0) {
		return TRUE;
	}
	else if (ereg(AUS_PHONE_NUMBER_REGEX, $trimmed, $match)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}
function trim_explode($delim, $text) {
	$in = explode($delim, $text);
	$out = array();
	foreach ($in as $a) {
		if (!empty($a))
			array_push($out, $a);
	}
	return $out;
}


function sendFailEmailToAdmin($msg='msg',$subject='(none)'){
	if($msg!=''){

	  $objGeneralSetting = new Models_GeneralSetting();
	  $cronSetting =  $objGeneralSetting->getGeneralSettingRec('ARCHIVE_CRON_DAYS');
	   
	  foreach($cronSetting as $k => $value)
	  {
		$arrSiteVar[$value['settings_varname']] = $value['settings_value'] ; 
	  }
 
	
	$arrSiteVar = isset($arrSiteVar)  ? $arrSiteVar : '' ; 
	
	$EMAIL_ID = $arrSiteVar['EMAIL_ID']; 
	
	  
      $mail = new Zend_Mail();
      $mail->setBodyHtml($msg);
      $mail->setFrom('cronjob@foreclosuresafari.tv', 'Cron job table faile');
      $mail->addTo('somebody_else@example.com', 'Some Recipient');
      $mail->setSubject( $subject );
      $mail->addTo("michael@burakccg.com", "Michael Burak");
	  $mail->addTo($EMAIL_ID, "");
	  if(isset($EMAIL_ID))
		$mail->send();	
	}
}



/**
 * Get Time Difference between Two Dates
 *
 * @param datetime start date
 * @param datetime end date
 * @return date difference
 */
function get_time_difference( $start, $end ,$array = 0)
{
    $uts['start']      =    strtotime( $start );
    $uts['end']        =    strtotime( $end );
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );
            
            if($array==0){
            	$total_hours = $days * 24;
	            $total_hours = $total_hours + $hours;
	            return $total_hours;
            }else if($array==1){
            	return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
            }
        }
        else
        {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }
    else
    {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
}

/**
 * send Mail To Recipient
 *
 * @param string $from
 * @param string $to
 * @param string $subject
 * @param string $body
 */
function sendMAIL($from,$to,$subject,$body)
{
	$mail = new Zend_Mail();
	$mail->setBodyHtml($body);
	$mail->setFrom($from, $from);
	$mail->setSubject($subject);
	$mail->addTo($to,$to);
	$mail->send();
}

/**
 * Get Mail Address from Mail string
 *
 * @param Mail string
 * @return Email Address
 */

function get_mail_address($mail_string = ""){
	$first_pos = strpos($mail_string, "<") + 1;
	$last_pos = strpos($mail_string, ">") ;
	$from_email = substr($mail_string, $first_pos, $last_pos-$first_pos);
	return $from_email;	
}


function sendAlert($body){
	
	$objSetting = new Models_Setting();
	$Setting = $objSetting->fetchEntry();
	
	$alertemail = $Setting['alert_email'];
	$alert_send_status = $Setting['alert_send_status'];
	
	if($alert_send_status=="Active")
	{
		$subject = "Alert from fifty100";
		$to = $alertemail;
		$from = FIFTY100_EMAIL;
		sendMAIL($from,$to,$subject,$body);
	}
}
