<?php

namespace Bixie\Emailsender\Model;

use Pagekit\Database\ORM\ModelTrait;

trait EmailTextTrait
{
	use ModelTrait {
		create as modelCreate;
	}

    /**
     * @param array $data
     * @return EmailText
     */
    public static function create ($data = []) {

        /** @var EmailText $text */
        $text = self::modelCreate(array_merge([
            'data' => [
                'markdown' => true,
                'file' => '',
                'from_email' => '',
                'from_name' => '',
                'to' => '',
                'cc' => '',
                'bcc' => '',
            ],
        ], $data));

        return $text;
    }
}
