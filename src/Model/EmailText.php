<?php

namespace Bixie\Emailsender\Model;


use Pagekit\Application as App;
use Bixie\Emailsender\Emailtype\Emailtype;
use Pagekit\Database\ORM\ModelTrait;
use Pagekit\System\Model\DataModelTrait;
use Pagekit\User\Model\AccessModelTrait;
use Pagekit\Util\Arr;

/**
 * @Entity(tableClass="@emailsender_emailtext",eventPrefix="emailsender_emailtext")
 */
class EmailText implements \JsonSerializable {

	use AccessModelTrait, DataModelTrait, ModelTrait;

	/** @Column(type="integer") @Id */
	public $id;
	/**
	 * @Column(type="string")
	 * @var string
	 */
	public $type = '';
	/**
	 * @Column(type="string")
	 * @var string
	 */
	public $subject;
	/**
	 * @Column(type="string")
	 * @var string
	 */
	public $content;
	/**
	 * @var Emailtype
	 */
	protected $emailtype;

    /** @var array */
    protected static $properties = [
        'type_label' => 'getEmailtypeLabel'
    ];

    /**
	 * @return Emailtype
	 */
	public function getEmailtype () {
		if (!isset($this->emailtype)) {
			$this->emailtype = App::get('emailtypes')->get($this->type);
		}
		return $this->emailtype;
	}

	/**
	 * @return string
	 */
	public function getEmailtypeLabel () {
		return $this->getEmailtype() ? $this->getEmailtype()->label : '';
	}

	/**
	 * @param array $to
	 * @return array
	 */
	public function getTo ($to = []) {
		return $this->mergeEmails($to, $this->get('to'));
	}

	/**
	 * @param array $cc
	 * @return array
	 */
	public function getCc ($cc = []) {
		return $this->mergeEmails($cc, $this->get('cc', []));
	}

	/**
	 * @param array $bcc
	 * @return array
	 */
	public function getBcc ($bcc = []) {
		return $this->mergeEmails($bcc, $this->get('bcc', []));
	}

	/**
	 * @param bool $replace
	 * @return string
	 */
	public function getSubject ($replace = true) {
		if ($replace) {
			$this->subject = $this->replaceString($this->subject, $this->emailtype->getVars());
		}
		return $this->subject;
	}

	/**
	 * @param bool $replace
	 * @return string
	 */
	public function getContent ($replace = true) {
		if ($replace) {
			$this->content = $this->replaceString($this->content, $this->emailtype->getVars());
		}
		return $this->content;
	}

	/**
	 * @param array|string $mail1
	 * @param array|string $mail2
	 * @return array
	 */
	protected function mergeEmails ($mail1 = [], $mail2 = []) {
		if (is_string($mail1)) {
			$mail1 = array_map('trim', explode(';', $this->replaceString($mail1, $this->emailtype->getVars())));
		}
		if (is_string($mail2)) {
			$mail2 = array_map('trim', explode(';', $this->replaceString($mail2, $this->emailtype->getVars())));
		}
		return array_filter(array_unique(array_merge((array)$mail1, (array)$mail2)), function ($email) {
		    return !empty($email);
		});
	}

	/**
	 * @param string $string
	 * @param array $data
	 * @param string $arraySeparator
	 * @return string
	 */
	public function replaceString ($string, $data, $arraySeparator = ', ') {

		$string = preg_replace_callback('/\$\$(.+?)\$\$/is', function($matches) use ($data, $arraySeparator) {
			$key = trim($matches[1]);
			$value = Arr::get($data, $key, '');
			return is_array($value) ? implode($arraySeparator, $value) : $value;
		}, $string);

		return $string;
	}


}