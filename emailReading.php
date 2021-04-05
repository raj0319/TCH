<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/include/constant.php");
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" >
	$(function(){
	var togglers = $$('div.toggler');
	if(togglers.length) var gmail = new Fx.Accordion(togglers,$$('div.body'));
	togglers.addEvent('click',function() { this.addClass('read').removeClass('unread'); });
	togglers[0].fireEvent('click'); //first one starts out read
});
</script>
<style>
	div.toggler				{ border:1px solid #ccc; background:url(gmail2.jpg) 10px 12px #eee no-repeat; cursor:pointer; padding:10px 32px; }
	div.toggler .subject	{ font-weight:bold; }
	div.read					{ color:#666; }
	div.toggler .from, div.toggler .date { font-style:italic; font-size:11px; }
	div.body					{ padding:10px 20px; }
</style>
<?php	
/* connect to gmail */
$hostname = '{imap.gmail.com/ssl/novalidate-cert}INBOX';
$username = 'rajmakadiaom@gmail.com';
$password = 'raj31291';

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* grab emails */
// $emails = imap_search($inbox,'SUBJECT "OverTime was edited recently"');
$emails = imap_search($inbox,'SUBJECT "Upgrade Your Living Rooms"');

/* if emails are returned, cycle through each... */
if($emails) {
	
	/* begin output var */
	$output = '';
	$isAttachment = false;
	/* put the newest emails on top */
	rsort($emails);
	
	/* for every email... */
	foreach($emails as $email_number) 
	{
		$message = "";
		$attachments = array();
		/* get information specific to this email */
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);
		$structure = imap_fetchstructure($inbox, $email_number);
		// print_r($structure);
		// exit;

        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts)) 
        {			
            for($i = 0; $i < count($structure->parts); $i++) 
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'filename') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if($structure->parts[$i]->ifparameters) 
                {
                    foreach($structure->parts[$i]->parameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'name') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if($attachments[$i]['is_attachment']) 
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

                    /* 3 = BASE64 encoding */
                    if($structure->parts[$i]->encoding == 3) 
                    { 
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                        // $message = base64_decode($message);
                    }
                    /* 4 = QUOTED-PRINTABLE encoding */
                    elseif($structure->parts[$i]->encoding == 4) 
                    { 
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                        // $message = quoted_printable_decode($message);
                    }
                }
				foreach($attachments as $attachment)
				{
					if($attachment['is_attachment'] == 1)
					{
						$isAttachment = true;
						$filename = $attachment['name'];
						if(empty($filename)) $filename = $attachment['filename'];

						if(empty($filename)) $filename = time() . ".txt";
						$folder = "Email-attachments";
						if(!is_dir($folder))
						{
							 mkdir($folder);
						}
						$fp = fopen("./". $folder ."/". $email_number . "-" . $filename, "w+");
						fwrite($fp, $attachment['attachment']);
						fclose($fp);
					}
				}
            }
		}	
		if(check_type($structure))
		{
			$message = imap_fetchbody($inbox, $email_number,"1.2"); ## GET THE BODY OF MULTI-PART MESSAGE
			if(!$message) {$message = '[NO TEXT ENTERED INTO THE MESSAGE]\n\n';}
		}
		else
		{
			$message = quoted_printable_decode(imap_fetchbody($inbox,$email_number,2));
			if(!$message) {$message = '[NO TEXT ENTERED INTO THE MESSAGE]\n\n';}
		}	
		
		/* output the email header information */
		$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
		$output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
		$output.= '<span class="from">'.$overview[0]->from.'</span>';
		$output.= '<span class="date">on '.$overview[0]->date.'</span>';
		$output.= '</div>';
		
		/* output the email body */
		$output.= '<div class="body">'.$message.'</div>';
	}

	imap_close($inbox);
	if($isAttachment)
	{
		echo $output."</br><div style='font-size:14px;background:yellow;' >all attachment Downloaded</div>";
	}
	else
	{
		echo $output;
	}
}


function check_type($structure) ## CHECK THE TYPE
{
  if($structure->subtype == "MIXED" && $structure->type) 
    {
     return(true); ## YES THIS IS A MULTI-PART MESSAGE
    }
else
    {
     return(false); ## NO THIS IS NOT A MULTI-PART MESSAGE
    }
}
 

/* close the connection */
imap_close($inbox);

 ?>