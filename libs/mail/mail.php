<?php

	/**
	 * summary
	 */
	class MailLib
	{

		/**
		 * summary
		 */
		public function sendMail(
			string $email      = '',
			string $message    = '',
			string $subject    = '',
			string $replyEmail = ''
		) : bool
		{
			if (
				preg_match('/^(.*?)@(.*?)\.(.*?)$/su', $email) > 0 &&
				strlen($message) > 0
			) {
				if (!strlen($subject) > 0) {
					$subject = 'Subject not set';
				}

				if (
					!preg_match('/^(.*?)@(.*?)\.(.*?)$/su', $replyEmail) &&
					isset($this->defaultEmail) &&
					preg_match('/^(.*?)@(.*?)\.(.*?)$/su', $this->defaultEmail)
				){
					$replyEmail = $this->defaultEmail;
				}

				if(!preg_match('/^(.*?)@(.*?)\.(.*?)$/su', $replyEmail)) {
					$replyEmail = 'noreply@noreply.noreply';
				}

				$emailHeaders = [
					"From: {$replyEmail}",
					'MIME-Version: 1.0',
					'Content-Type: text/html; charset=UTF-8',
					'X-Mailer: PHP/'.phpversion()
				];
				$emailHeaders = implode("\r\n", $emailHeaders);

				$res = mail($email, $subject, $message, $emailHeaders);

				if (!$res) {
					$failedMailData = [
						'email'        => $email,
						'emailHeaders' => $emailHeaders,
						'subject'      => $subject,
						'message'      => $message,
						'time'         => date('Y:m:d H:i:S'),
						'timestamp'    => time()
					];
					$failedMailFile =__DIR__
						.'/../../res/mail/failed/'
						.hash('md5', $email)
						.'_'
						.date('Y-m-d_H-i-s')
						.'.txt';
					$failedMailData = json_encode($failedMailData);
					file_put_contents($failedMailData, $failedMailFile);
				}

				return $res;
			}
		}
	}
?>