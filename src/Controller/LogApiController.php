<?php

namespace Bixie\Emailsender\Controller;

use Pagekit\Application as App;
use Bixie\Emailsender\Model\EmailLog;

/**
 * @Route("log", name="log")
 * @Access("emailsender: view logs")
 */
class LogApiController {

	/**
	 * @Route("/", methods="GET")
	 * @Request({"filter": "array", "page":"int"})
	 */
	public function indexAction ($filter = [], $page = 0) {
		$query  = EmailLog::query();
		$filter = array_merge(array_fill_keys(['ext_key', 'type', 'search', 'order', 'limit'], ''), $filter);
		extract($filter, EXTR_SKIP);

		if ($ext_key) {
			$query->where(['ext_key' => $ext_key]);
		}

		if ($type) {
			$query->where(['type' => $type]);
		}

		if ($search) {
			$query->where(function ($query) use ($search) {
				$query->orWhere([
					'from_name LIKE :search', 'from_email LIKE :search', 'recipients LIKE :search', 'cc LIKE :search',
					'bcc LIKE :search', 'subject LIKE :search', 'content LIKE :search'
				], ['search' => "%{$search}%"]);
			});
		}

		if (preg_match('/^(sent|recipients|type)\s(asc|desc)$/i', $order, $match)) {
			$order = $match;
		} else {
			$order = [1=>'sent', 2=>'desc'];
		}

		$default = 25;
		$limit   = min(max(0, $limit), $default) ?: $default;
		$count   = $query->count();
		$pages   = ceil($count / $limit);
		$page    = max(0, min($pages - 1, $page));
		$logs   = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->get());

		return compact('logs', 'pages', 'count');
	}

	/**
	 * @Route("/detail")
	 * @Request({"log_id"}, csrf=true)
	 */
	public function detailAction ($id) {

		if (!$log = EmailLog::where(['id = ?'], [$id])->first()) {
			App::abort(404, 'Log not found.');
		}

		return $log;
	}

	/**
     * @Access("emailsender: manage logs")
	 * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
	 * @Request({"id": "int"}, csrf=true)
	 */
	public function deleteAction ($id) {
		if ($text = EmailLog::find($id)) {

			$text->delete();
		}

		return ['message' => 'success'];
	}

	/**
     * @Access("emailsender: manage logs")
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
