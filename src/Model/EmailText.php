<?php

namespace Bixie\Emailsender\Model;


use Pagekit\Database\ORM\ModelTrait;
use Pagekit\System\Model\DataModelTrait;
use Pagekit\User\Model\AccessModelTrait;

/**
 * @Entity(tableClass="@emailsender_emailtext",eventPrefix="emailsender_emailtext")
 */
class Emailtext implements \JsonSerializable {

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


}