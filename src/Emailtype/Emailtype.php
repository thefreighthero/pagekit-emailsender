<?php

namespace Bixie\Emailsender\Emailtype;

use Pagekit\Application as App;
use Pagekit\Util\Arr;


class Emailtype implements \JsonSerializable {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $label;

	/**
     * Class references to objects to include in the type
	 * @var array
	 */
	public $classes = [];

	/**
     * manually defined values to be replaced in email
	 * @var array
	 */
	public $values = [];

	/**
     * Processed plain values for replacement
	 * @var array
	 */
	protected $vars;
	/**
     * Serializable objects to extract data from
	 * @var array
	 */
	protected $objects = [];
	/**
     * Array data to hydrate objects with
	 * @var array
	 */
	protected $object_data = [];

	/**
	 * Emailtype constructor.
	 * @param string $name
	 * @param array  $data
	 */
	public function __construct ($name, array $data) {
		$this->name = $name;
		foreach (get_object_vars($this) as $key => $default) {
			$this->$key = Arr::get($data, $key, $default);
		}
		if (!isset($this->classes['user'])) {
            $this->classes['user'] = class_exists('Bixie\Userprofile\User\ProfileUser') ?
                'Bixie\Userprofile\User\ProfileUser' :
                'Pagekit\User\Model\User';
        }
	}

	/**
	 * @return string
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * @param $key
	 * @param $data
	 * @return Emailtype
	 */
	public function addData ($key, $data) {
		if ($key == 'values') {
			$this->values = $data;
		}
		if ($data instanceof \JsonSerializable) {
			$this->objects[$key] = $data;
		}
		if (isset($this->classes[$key])) {
			$this->object_data[$key] = $data;
		}
		return $this;
	}

	/**
     * Process objects and values to replacable vars
	 * @return array
	 */
	public function getVars ($flat = true) {
		if (!isset($this->vars)) {
			$this->vars = [];
			foreach ($this->classes as $key => $class) {
				if (!isset($this->objects[$key]) && class_exists($class)) {
                    $data = isset($this->object_data[$key]) ? $this->object_data[$key] : [];
					$object = method_exists($class, 'create') ? $class::create((array)$data) : new $class();
					$this->addData($key, $object);
				}
				if (isset($this->objects[$key])) {
					//get data via jsonSerialize
					$this->vars[$key] = json_decode(json_encode($this->objects[$key], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), true);
				}
			}
			foreach ($this->values as $key => $value) {
				$this->vars['values'][$key] = $value;
			}
		}
		return Arr::flatten($this->vars);
	}

	/**
     * Replacable keys
	 * @return mixed
	 */
	public function getKeys () {
		return array_keys($this->getVars());
	}

	/**
	 * @param array $data
	 * @param array $ignore
	 * @return array
	 */
	public function toArray ($data = [], $ignore = []) {
		return array_diff_key(array_merge([
			'name' => $this->name,
			'label' => $this->label ? : $this->name,
			'keys' => $this->getKeys()
		], $data), array_flip($ignore));
	}

	/**
	 * @return array
	 */
	function jsonSerialize () {
		return $this->toArray();
	}


}