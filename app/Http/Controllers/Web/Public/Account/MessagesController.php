<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Web\Public\Account;

use App\Helpers\UrlGen;
use App\Http\Controllers\Web\Public\Account\Traits\MessagesTrait;
use App\Http\Requests\ReplyMessageRequest;
use App\Http\Requests\SendMessageRequest;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class MessagesController extends AccountBaseController
{
	use MessagesTrait;
	
	private $perPage = 10;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->perPage = (is_numeric(config('settings.list.items_per_page'))) ? config('settings.list.items_per_page') : $this->perPage;
	}
	
	/**
	 * Show all the message threads to the user.
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function index()
	{
		// Call API endpoint
		$endpoint = '/threads';
		$queryParams = [];
		if (request()->filled('filter')) {
			$queryParams['filter'] = request()->get('filter');
		}
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams);
		
		$apiMessage = $this->handleHttpError($data);
		$apiResult = data_get($data, 'result');
		
		// Meta Tags
		MetaTag::set('title', t('messenger_inbox'));
		MetaTag::set('description', t('messenger_inbox'));
		
		if (request()->ajax()) {
			$threads = (array)data_get($apiResult, 'data');
			$totalThreads = (array)data_get($apiResult, 'meta.total');
			
			$result = [
				'threads' => view('account.messenger.threads.threads', ['totalThreads' => $totalThreads, 'threads' => $threads])->render(),
				'links'   => view('account.messenger.threads.links', ['apiResult' => $apiResult])->render(),
			];
			
			return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
		}
		
		return view('account.messenger.index', compact('apiResult'));
	}
	
	/**
	 * Shows a message thread.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */ 
	public function show($id)
	{
		// Call API endpoint
		$endpoint = '/threads/' . $id;
		$queryParams = [
			'embed'   => 'user,post,messages,participants',
			'perPage' => $this->perPage, // for the thread's messages
		];
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams);
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : null;
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			$msg = $message ?? t('thread_not_found', ['id' => $id]);
			flash($msg)->error();
			
			return redirect('account/messages');
		}
		
		$thread = data_get($data, 'result');
		
		// Message Collection
		// $errorMessage = data_get($thread, 'messages.message');
		$apiResult = data_get($thread, 'messages.result');
		$messages = (array)data_get($apiResult, 'data');
		$totalMessages = (int)data_get($apiResult, 'meta.total', 0);
		$linksRender = view('account.messenger.messages.pagination', ['apiResult' => $apiResult])->render();
		
		// Meta Tags
		MetaTag::set('title', t('Messages Received'));
		MetaTag::set('description', t('Messages Received'));
		
		// Reverse the collection order like Messenger
		$messages = collect($messages)->reverse()->toArray();
		
		if (request()->ajax()) {
			$result = [
				'totalMessages' => $totalMessages,
				'messages'      => view(
					'account.messenger.messages.messages',
					[
						'thread'        => $thread,
						'totalMessages' => $totalMessages,
						'messages'      => $messages,
					]
				)->render(),
				'links'         => $linksRender,
			];
			
			return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
		}
		
		return view('account.messenger.show', compact('thread', 'totalMessages', 'messages', 'linksRender'));
	}
	
	/**
	 * Stores a new message thread.
	 * Contact the Post's Author
	 * NOT use AJAX
	 *
	 * @param $postId
	 * @param \App\Http\Requests\SendMessageRequest $request
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store($postId, SendMessageRequest $request)
	{
		request()->merge(['post_id' => $postId]);
		
		// Call API endpoint
		$endpoint = '/threads';
		$data = makeApiRequest('post', $endpoint, $request->all(), [], true);
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			flash($message)->error();
			
			return redirect()->back()->withInput($request->except('filename'));
		}
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
		} else {
			flash($message)->error();
		}
		
		// Get Post
		$post = data_get($data, 'extra.post');
		
		if (!empty($post)) {
			return redirect(UrlGen::postUri($post));
		} else {
			return redirect()->back();
		}
	}
	
	/**
	 * Adds a new message to a current thread.
	 *
	 * @param $id
	 * @param \App\Http\Requests\ReplyMessageRequest $request
	 * @return \Illuminate\Http\JsonResponse|void
	 */
	public function update($id, ReplyMessageRequest $request)
	{
		if (!request()->ajax()) {
			return;
		}
		
		// Call API endpoint
		$endpoint = '/threads/' . $id;
		$data = makeApiRequest('post', $endpoint, $request->all(), [], true);
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		$result = [
			'success' => (bool)data_get($data, 'success'),
			'msg'     => $message,
		];
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
		}
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * Actions on the Threads
	 *
	 * @param $threadId
	 * @return \Illuminate\Http\JsonResponse|void
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function actions($threadId = null)
	{
		if (!request()->ajax()) {
			return;
		}
		
		// Call API endpoint
		$endpoint = '/threads/bulkUpdate' . $this->getSelectedIds($threadId);
		$data = makeApiRequest('post', $endpoint, request()->all());
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		$result = [
			'type'    => request()->get('type'),
			'success' => (bool)data_get($data, 'success'),
			'msg'     => $message,
		];
		if (!empty($threadId)) {
			$result['baseUrl'] = request()->url();
		}
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			$result['success'] = false;
		}
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * Delete Thread
	 *
	 * @param null $threadId
	 * @return \Illuminate\Http\JsonResponse|void
	 */
	public function destroy($threadId = null)
	{
		if (!request()->ajax()) {
			return;
		}
		
		// Call API endpoint
		$endpoint = '/threads' . $this->getSelectedIds($threadId);
		$data = makeApiRequest('delete', $endpoint, request()->all());
		
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';
		
		$result = [
			'type'    => 'delete',
			'success' => (bool)data_get($data, 'success'),
			'msg'     => $message,
		];
		if (!empty($threadId)) {
			$result['baseUrl'] = request()->url();
		}
		
		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			$result['success'] = false;
		}
		
		return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
	}
}
