<?php

namespace Bixie\Emailsender\Event;

use Bixie\Emailsender\Model\EmailText;
use Pagekit\Event\Event;
use Pagekit\Mail\Message;

class EmailPrepareEvent extends Event
{
	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var array
	 */
	protected $images = [];
	/**
	 * @var Message
	 */
	protected $message;
	/**
	 * @var EmailText
	 */
	protected $text;

	/**
	 * Constructor.
	 * @param string $name
	 * @param string $content
	 * @param Message $message
	 * @param array  $parameters
	 */
	public function __construct ($name, $content, Message $message, EmailText $text, array $parameters = []) {
		parent::__construct($name, $parameters);

		$this->content = $content;
		$this->message = $message;
		$this->text = $text;
	}

	/**
	 * @return string
	 */
	public function getContent () {
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent ($content) {
		$this->content = $content;
	}

	/**
	 * @return array
	 */
	public function getImages () {
		return $this->images;
	}

	/**
	 * @param array $images
	 */
	public function setImages ($images) {
		$this->images = $images;
	}

	/**
	 * @return Message
	 */
	public function getMessage () {
		return $this->message;
	}

	/**
	 * @return EmailText
	 */
	public function getText () {
		return $this->text;
	}



}
