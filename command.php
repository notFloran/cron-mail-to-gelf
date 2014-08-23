<?php

require 'vendor/autoload.php';

include 'config.php';

# Graylog
$transport = new Gelf\Transport\UdpTransport($config['graylog_host'], $config['graylog_port']);
$publisher = new Gelf\Publisher();
$publisher->addTransport($transport);

# Imap server
$server = new \Fetch\Server($config['imap_host'], $config['imap_port']);
$server->setAuthentication($config['imap_user'], $config['imap_password']);

if(!$server->hasMailBox('Cron')) {
	echo "No mailbox 'Cron'";
	return;
}

$server->setMailbox('Cron');

$messages = $server->search('UNSEEN');
foreach ($messages as $message) {
	$headers = $message->getHeaders();
	$messageBody = $message->getMessageBody();
	$host = reset($headers->from)->host;	

	$date = $message->getDate();
	$date = floatval($date . substr((string)microtime(true),strlen($date)));

	$lines = explode("\n", $messageBody);

	foreach($lines as $line) {
		$date = $date+0.0010;

		$gelfMessage = new Gelf\Message();
		$gelfMessage
			->setShortMessage($line)
			->setLevel(\Psr\Log\LogLevel::NOTICE)
			->setFacility("cron-email")
			->setHost($host)
			->setTimestamp($date)
			->setAdditional('email-uid', $message->getUid())
			->setAdditional('email-subject', $message->getSubject())
		;
		$publisher->publish($gelfMessage);
	}
}
