<?php

namespace Bixie\Emailsender\Plugin;

use Bixie\Emailsender\Model\EmailText;
use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Bixie\Emailsender\Event\EmailPrepareEvent;
use Symfony\Component\Routing\Generator\UrlGenerator;

class MailLinksPlugin implements EventSubscriberInterface {

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * MailImagesPlugin constructor.
	 * @param array $config
	 */
	public function __construct ($config) {
		$this->config = $config;
	}

	/**
	 * Content plugins callback.
	 * @param EmailPrepareEvent $event
	 */
	public function onEmailPrepare (EmailPrepareEvent $event) {

		$content = $this->replaceLinkHrefs($event->getContent(), $event->getText());

		$event->setContent($content);
	}

	/**
	 * @param         $content
	 * @param EmailText $text
	 * @return string
	 */
	protected function replaceLinkHrefs ($content, EmailText $text) {
		try {
			$doc = new \DOMDocument();
			$doc->loadHTML($content);
			$tags = $doc->getElementsByTagName('a');
			$site_base = App::url()->get('', [], UrlGenerator::ABSOLUTE_URL);
			$params = array_reduce($this->config['url_parameters'], function ($params, $param) use ($text) {
				$params[$param['key']] = $param['value'] == '$$text.type$$' ? $text->type : $param['value'];
				return $params;
			}, []);
			foreach ($tags as $tag) {
				$new_href = ltrim($tag->getAttribute('href'), '/');
				//skip external links
				if (substr($new_href, 0, 4) == 'http' && stripos($new_href, $site_base) === false) {
					continue;
				}
				//strip root
				if (stripos($new_href, $site_base) === 0) {
					$new_href = str_replace($site_base, '', $new_href);
				}
				if (count($params) && $this->config['add_url_params']) {
					$new_href = App::url()->get($new_href, $params, UrlGenerator::ABSOLUTE_URL);
				} else {
					$new_href = $site_base . $new_href;
				}
				$tag->setAttribute('href', $new_href);
			}
			return $doc->saveHTML();
		} catch (\Exception $e) {
			return $content;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function subscribe () {
		return [
			'emailsender.prepare' => ['onEmailPrepare', 0]
		];
	}
}
