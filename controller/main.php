<?php
/**
*
* @package phpBB Extension - Wiki
 * @copyright (c) 2015 tas2580 (https://tas2580.net)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace tas2580\wiki\controller;

class main
{
	/* @var \phpbb\controller\helper */
	protected $helper;
	/* @var \phpbb\request\request */
	protected $request;
	/* @var \phpbb\template\template */
	protected $template;
	/* @var \phpbb\user */
	protected $user;
	/* @var \tas2580\wiki\wiki */
	protected $wiki;
	/** @var string phpbb_root_path */
	protected $phpbb_root_path;
	/** @var string php_ext */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper		$helper				Controller helper object
	* @param \phpbb\request\request		$request				Request object
	* @param \phpbb\template\template	$template				Template object
	* @param \phpbb\user				$user				User object
	* @param \tas2580\wiki\wiki			$wiki					Wiki object
	* @param string					$phpbb_root_path
	* @param string					$php_ext
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \tas2580\wiki\wiki\edit $edit, \tas2580\wiki\wiki\diff $diff, \tas2580\wiki\wiki\view $view, $phpbb_root_path, $php_ext)
	{
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->edit = $edit;
		$this->diff = $diff;
		$this->view = $view;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Display the wiki index page
	 *
	 * @return object
	 */
	public function index()
	{
		$article = $this->request->variable('article', '', true);
		return $this->article($article);
	}

	/**
	 * Display an article
	 *
	 * @param	string	$article	URL of the article
	 * @return	object
	 */
	public function article($article)
	{
		$this->user->add_lang_ext('tas2580/wiki', 'common');

		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'		=> $this->user->lang['WIKI'],
			'U_VIEW_FORUM'	=> $this->helper->route('tas2580_wiki_index', array()),
		));

		$this->template->assign_vars(array(
			'WIKI_FOOTER'		=> $this->user->lang('WIKI_FOOTER', base64_decode('aHR0cHM6Ly90YXMyNTgwLm5ldA=='), base64_decode('dGFzMjU4MA==')),
		));

		include($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		include($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);

		$action = $this->request->variable('action', '');
		$id = $this->request->variable('id', 0);

		if ($action === 'edit')
		{
			return $this->edit->edit_article($article);
		}
		else if ($action === 'versions')
		{
			return $this->diff->view_versions($article);
		}
		else if ($action === 'compare')
		{
			$from = $this->request->variable('from', 0);
			$to = $this->request->variable('to', 0);
			return $this->diff->compare_versions($article, $from, $to);
		}
		else if ($action === 'delete')
		{
			return $this->edit->delete($id);
		}

		return $this->view->view_article($article, $id);
	}
}
