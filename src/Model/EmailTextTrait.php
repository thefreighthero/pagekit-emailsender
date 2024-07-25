<?php

namespace Bixie\Emailsender\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;
use Symfony\Component\Routing\Generator\UrlGenerator;

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
    /**
     * @Saving
     */
    public static function saving($event, EmailText $emailText, $data) {

        $content = $emailText->content;

        // Regular expression to find img tags with relative paths
        $pattern = '/<img\s+[^>]*src="([^"]+)"[^>]*>/i';

        // Set base url
        $site_base = App::url()->get('', [], UrlGenerator::ABSOLUTE_URL);

        // Callback function to replace the relative path with absolute path
        $callback = function($matches)  use($site_base) {
            $relative_path = $matches[1];
            // Check if the path is already absolute
            if (!preg_match('/^(http:\/\/|https:\/\/|\/)/i', $relative_path)) {
                $absolute_path = rtrim($site_base, '/') . '/' . ltrim($relative_path, '/');
            } else {
                $absolute_path = $relative_path;
            }
            // Replace the relative path with the absolute path in the src attribute
            return str_replace($relative_path, $absolute_path, $matches[0]);
        };

        // Replace all occurrences in the HTML string
        $content = preg_replace_callback($pattern, $callback, $content);

        $emailText->content = $content;
    }
}
