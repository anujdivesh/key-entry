<?php
require 'phpmailer/PHPMailerAutoload.php';


class sendmail{
	public function mail($toAdd, $subject, $body)
	{
		$mail = new PHPMailer;
		$mail->isSMTP();
		#$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = "simoli.met.gov.fj";
		$mail->Port = 25;
		$mail->SMTPAuth = false;
		$mail->SMTPSecure = false;
		$mail->SMTPAutoTLS = false;
		$mail->setFrom("argent@met.gov.fj", "Fiji Meteorological Service");
		$mail->addAddress($toAdd);
		$mail->addReplyTo("argent@met.gov.fj");
		$mail->isHTML(true);

		$mail->Subject = $subject;
		$mail->Body = $body;

		if(!$mail->send()) 
		{
			return false;
		} 
		else 
		{
			return true;
		}
	}
	
	public function mailcopy($toAdd, $CC, $subject, $body)
	{
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = "server.met.gov.fj";
		$mail->Port = 25;
		$mail->SMTPAuth = false;
		$mail->SMTPSecure = false;
		$mail->setFrom("argent@met.gov.fj", "QMS");
		$mail->addAddress($toAdd);
		$mail->AddCC($CC);
		$mail->addReplyTo("argent@met.gov.fj");
		$mail->isHTML(true);

		$mail->Subject = $subject;
		$mail->Body = $body;

		if(!$mail->send()) 
		{
			echo "Mailer Error: " . $mail->ErrorInfo;
		} 
		else 
		{
			echo "Message has been sent successfully";
		}

		return true;
	}
	
	public function mailmulti($toAdd, $subject, $body)
	{
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = "server.met.gov.fj";
		$mail->Port = 25;
		$mail->SMTPAuth = false;
		$mail->SMTPSecure = false;
		$mail->SMTPAutoTLS = false;
		$mail->setFrom("argent@met.gov.fj", "QMS");
		
		foreach($toAdd as $row){
			$mail->addAddress($row['email']);
		}
		
		$mail->addReplyTo("argent@met.gov.fj");
		$mail->isHTML(true);

		$mail->Subject = $subject;
		$mail->Body = $body;

		if(!$mail->send()) 
		{
			echo "Mailer Error: " . $mail->ErrorInfo;
		} 
		else 
		{
			echo "Message has been sent successfully";
		}

		return true;
	}
}


?>