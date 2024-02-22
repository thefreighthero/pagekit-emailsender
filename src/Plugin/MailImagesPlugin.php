<?php

namespace Bixie\Emailsender\Plugin;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Bixie\Emailsender\Event\EmailPrepareEvent;
use Pagekit\Mail\Message;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Generator\UrlGenerator;

class MailImagesPlugin implements EventSubscriberInterface {

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

        $content = $this->replaceImgSrc($event->getContent(), $event->getMessage());

        $event->setContent($content);
    }

    /**
     * @param         $content
     * @param Message $message
     * @return string
     */
    protected function replaceImgSrc ($content, Message $message) {
        try {
            $doc = new \DOMDocument();
            $doc->loadHTML($content);
            $tags = $doc->getElementsByTagName('img');
            $site_base = App::url()->get('', [], UrlGenerator::ABSOLUTE_URL);
            foreach ($tags as $tag) {
                $new_src = ltrim($tag->getAttribute('src'), '/');
                //skip external images
                if (substr($new_src, 0, 4) == 'http' && stripos($new_src, $site_base) === false) {
                    continue;
                }
                //strip root
                if (stripos($new_src, $site_base) === 0) {
                    $new_src = str_replace($site_base, '', $new_src);
                }
                // Brevo does not support embedded images
                if (!$this->config['use_brevo']
                    and $this->config['embed_images']
                    and $image_path = App::locator()->get($new_src)
                    and $file = new File($image_path)
                    and $file->getFileInfo()->getSize() < ($this->config['embed_images_maxsize'] * 1024)
                    and $cid = $message->embedFile($image_path)
                ) {
                    //replace image with cid
                    $new_src = $cid;
                } else {
                    $new_src = $site_base . $new_src;
                }
                $tag->setAttribute('src', $new_src);
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
