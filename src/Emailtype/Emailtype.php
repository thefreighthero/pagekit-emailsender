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
	 * @var array
	 */
	public $classes = [];

	/**
	 * @var array
	 */
	public $values = [];

	/**
	 * @var array
	 */
	protected $vars;
	/**
	 * @var array
	 */
	protected $objects = [];

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
		return $this;
	}

	/**
	 * @return array
	 */
	public function getVars ($flat = true) {
		if (!isset($this->vars)) {
			$this->vars = [];
			foreach ($this->classes as $key => $class) {
				if (!isset($this->objects[$key]) && class_exists($class)) {
					$object = method_exists($class, 'create') ? $class::create() : new $class();
					$this->addData($key, $object);
				}
				if (isset($this->objects[$key])) { 
					//get data via jsonSerialize
					$this->vars[$key] = json_decode(json_encode($this->objects[$key], JSON_NUMERIC_CHECK), true);
				}
			}
			foreach ($this->values as $key => $value) {
				$this->vars['values'][$key] = $value;
			}
		}
		return Arr::flatten($this->vars);
	}

	/**
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