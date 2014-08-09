<?php
namespace TeenQuotes\Mail\Transport;

use Illuminate\Mail\Transport\MandrillTransport as BaseMandrillTransport;
use Swift_Mime_Message;

class MandrillTransport extends BaseMandrillTransport {

	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$client = $this->getHttpClient();

		$client->post('https://mandrillapp.com/api/1.0/messages/send-raw.json', [
			'body' => [
				'key'         => $this->key,
				'raw_message' => (string) $message,
				// see https://mandrillapp.com/api/docs/messages.JSON.html#method=send-raw
				'async'       => true,
			],
		]);
	}
}