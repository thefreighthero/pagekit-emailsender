<?php

namespace Bixie\Emailsender\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Bixie\Emailsender\Model\EmailText;
use Twig_Environment;
use Twig_Loader_Array;

/**
 * @Route("text", name="text")
 * @Access("emailsender: manage texts")
 */
class TextApiController {

	/**
	 * @Route("/", methods="GET")
	 * @Request({"filter": "array", "page":"int"})
	 */
	public function indexAction ($filter = [], $page = 0) {
		$query = EmailText::query();
		$filter = array_merge(array_fill_keys(['type', 'order', 'search', 'limit'], ''), $filter);

		extract($filter, EXTR_SKIP);

		if (!empty($type)) {
			$query->where('type = ?', [$type]);
		}

		if (!preg_match('/^(subject|type)\s(asc|desc)$/i', $order, $order)) {
			$order = [1 => 'subject', 2 => 'asc'];
		}

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['description LIKE :search', 'subject LIKE :search', 'content LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

		$limit = (int)$limit ?: 20;
		$count = $query->count();
		$pages = ceil($count / $limit);
		$page = max(0, min($pages - 1, $page));

		$texts = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->get());

		return compact('texts', 'pages', 'count');

	}

	/**
	 * @Route("/", methods="POST")
	 * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
	 * @Request({"text": "array", "id": "int"}, csrf=true)
	 */
	public function saveAction ($data, $id = 0) {

		if (!$text = EmailText::find($id)) {
			$text = EmailText::create();
			unset($data['id']);
		}

		//test Twig syntax
        $loader = new Twig_Loader_Array([
            'emailtext' => $data['content'],
        ]);
        try {
            $twig = new Twig_Environment($loader);
            $twig->render('emailtext', $data);
        } catch (\Twig_Error_Loader $e) {
            App::abort(400, 'Error in template loader: ' . $e->getMessage());
        } catch (\Twig_Error_Runtime $e) {
            App::abort(400, 'Error in template parsing: ' . $e->getMessage());
        } catch (\Twig_Error_Syntax $e) {
            App::abort(400, 'Error in template syntax: ' . $e->getMessage());
        }


        try {

			$text->save($data);

		} catch (Exception $e) {
			App::abort(400, $e->getMessage());
		}

		return ['message' => 'success', 'text' => $text];
	}

	/**
	 * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
	 * @Request({"id": "int"}, csrf=true)
	 */
	public function deleteAction ($id) {
		if ($text = EmailText::find($id)) {

			$text->delete();
		}

		return ['message' => 'success'];
	}

	/**
	 * @Route("/bulk", methods="DELETE")
	 * @Request({"ids": "array"}, csrf=true)
	 */
	public function bulkDeleteAction ($ids = []) {
		foreach (array_filter($ids) as $id) {
			$this->deleteAction($id);
		}

		return ['message' => 'success'];
	}

}
