<?php

namespace Bixie\Emailsender\Model;


use Pagekit\Database\ORM\ModelTrait;
use Pagekit\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@emailsender_emaillog",eventPrefix="emailsender_emaillog")
 */
class EmailLog implements \JsonSerializable {

	use DataModelTrait, ModelTrait;

	/** @Column(type="integer") @Id */
	public $id;
	/**
	 * @var \DateTime
	 * @Column(type="datetime")
	 */
	public $sent;
	/**
	 * @Column(type="string")
	 * @var string
	 */
	public $type;
	/**
	 * @Column(type="string")
	 * @var string
	 */
	public $from_name;
	/**
	 * @Column(type="string")
	 * @var string
	 */
	public $from_email;
	/**
	 * @Column(type="simple_array")
	 * @var array
	 */
	public $recipients;
	/**
	 * @Column(type="simple_array")
	 * @var array
	 */
	public $cc;
	/**
	 * @Column(type="simple_array")
	 * @var array
	 */
	public $bcc;
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