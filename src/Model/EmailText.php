<?php

namespace Bixie\Emailsender\Model;


use Pagekit\Application as App;
use Bixie\Emailsender\Emailtype\Emailtype;
use Pagekit\System\Model\DataModelTrait;
use Pagekit\User\Model\AccessModelTrait;
use Pagekit\Util\Arr;
use Twig_Environment;
use Twig_Loader_Array;

/**
 * @Entity(tableClass="@emailsender_emailtext",eventPrefix="emailsender_emailtext")
 */
class EmailText implements \JsonSerializable {

	use AccessModelTrait, DataModelTrait, EmailTextTrait;

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
	public $description;
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
     * @param $data
     */
    public function __construct($data) {
        foreach(['id', 'type', 'description', 'subject', 'content', 'emailtype', 'roles', 'data'] as $field) {
            if(isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    public function getId () {
        return $this->id;
    }

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
	public function getFromName ($replace = true) {
        if ($replace) {
            return $this->replaceString($this->get('from_name', ''), $this->emailtype->getVars(false));
        }
        return $this->get('from_name', '');
    }

	/**
     * @param bool $replace
	 * @return string
	 */
	public function getFromEmail ($replace = true) {
        if ($replace) {
            return $this->replaceString($this->get('from_email', ''), $this->emailtype->getVars(false));
        }
        return $this->get('from_email', '');
    }

	/**
	 * @param bool $replace
	 * @return string
	 */
	public function getSubject ($replace = true) {
		if ($replace) {
			$this->subject = $this->replaceString($this->subject, $this->emailtype->getVars(false));
		}
		return $this->subject;
	}

	/**
	 * @param bool $replace
	 * @return string
	 */
	public function getContent ($replace = true) {
		if ($replace) {
			$this->content = $this->replaceString($this->content, $this->emailtype->getVars(false));
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
			$mail1 = array_map('trim', explode(';', $this->replaceString($mail1, $this->emailtype->getVars(false))));
		}
		if (is_string($mail2)) {
			$mail2 = array_map('trim', explode(';', $this->replaceString($mail2, $this->emailtype->getVars(false))));
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

	    //replace legacy vars `$$ foo.bar $$`
        $pattern = '/\$\$(.+?)\$\$/is';
        if (preg_match($pattern, $string)) {
            $flattened = Arr::flatten($data);
            $string = preg_replace_callback($pattern, function ($matches) use ($flattened, $arraySeparator) {
                $key = trim($matches[1]);
                $value = Arr::get($flattened, $key, '');
                if (is_array($value)) {
                    $value = implode($arraySeparator, $value);
                } elseif (is_bool($value)) {
                    $value = $value ? __('Yes') : __('No');
                }
                return $value;
            }, $string);
        }

        $loader = new Twig_Loader_Array([
            'emailtext' => $string,
        ]);
        try {
            $twig = new Twig_Environment($loader);

            $string = $twig->render('emailtext', $data);

        } catch (\Twig_Error $e) {}

        return $string;
	}


}