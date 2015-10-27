<?php
/**
* @author Anvar [http://bb3.mobi]
* @version 1.0.1
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

namespace bb3mobi\NotFound\controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class notfound
{
	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, $php_ext)
	{
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->text = $config_text;
		$this->template = $template;
		$this->db = $db;
		$this->phpEx = $php_ext;
	}

	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		if ($event->getException() instanceof NotFoundHttpException)
		{
			$this->user->add_lang_ext('bb3mobi/NotFound', 'info_acp_notfound');

			// Send a proper content-language to the output
			$user_lang = $this->user->lang['USER_LANG'];
			if (strpos($user_lang, '-x-') !== false)
			{
				$user_lang = substr($user_lang, 0, strpos($user_lang, '-x-'));
			}

			$not_found_text = $this->text->get('notfound_text');
			//$not_found_text = preg_replace("/\r\n|\r|\n/", '<br />', $not_found_text);
			$not_found_text = htmlspecialchars_decode($not_found_text);

			$this->template->assign_vars(array(
				'SITENAME'				=> $this->config['sitename'],
				'SITE_DESCRIPTION'		=> $this->config['site_desc'],
				'PAGE_TITLE'			=> $this->user->lang['ACP_NOT_FOUND_TITLE'],
				'SCRIPT_NAME'			=> str_replace('.' . $this->phpEx, '', $this->user->page['page_name']),
				'SITE_LOGO_IMG'			=> $this->user->img('site_logo'),
				'S_DISPLAY_SEARCH'		=> (!$this->config['load_search']) ? 0 : (isset($this->auth) ? ($this->auth->acl_get('u_search') && $this->auth->acl_getf_global('f_search')) : 1),
				'S_CONTENT_DIRECTION'	=> $this->user->lang['DIRECTION'],
				'S_CONTENT_ENCODING'	=> 'UTF-8',
				'S_USER_LANG'			=> $user_lang,

				'U_SITE_HOME'			=> $this->config['site_home_url'],
				// Link correct
				'U_INDEX'				=> generate_board_url() . "/index." . $this->phpEx,
				'U_SEARCH'				=> generate_board_url() . "/search." . $this->phpEx,

				'NOT_FOUND_PAGE'		=> $this->user->lang['ACP_NOT_FOUND'],
				'NOT_FOUND_TITLE'		=> $this->user->lang['ACP_NOT_FOUND_TITLE'],
				'NOT_FOUND_AUTHOR'		=> $this->user->lang['NOT_FOUND_AUTHOR'],
				'NOT_FOUND_TEXT'		=> $not_found_text,

				'T_THEME_PATH'			=> generate_board_url() . "/styles/" . rawurlencode($this->user->style['style_path']) . '/theme',
				'T_STYLESHEET_BOARD_LINK'		=> generate_board_url() . "/styles/" . rawurlencode($this->user->style['style_path']) . '/theme/stylesheet.css?assets_version=' . $this->config['assets_version'],
				'T_STYLESHEET_RESPONSIVE_LINK'	=> generate_board_url() . "/styles/" . rawurlencode($this->user->style['style_path']) . '/theme/responsive.css?assets_version=' . $this->config['assets_version'],

				'TRANSLATION_INFO'		=> (!empty($this->user->lang['TRANSLATION_INFO'])) ? $this->user->lang['TRANSLATION_INFO'] : '',
				'CREDIT_LINE'			=> $this->user->lang('POWERED_BY', '<a href="https://www.phpbb.com/">phpBB</a>&reg; Forum Software &copy; phpBB Limited'),
			));

			// Recent Topics
			if ($this->config['notfound_recent_topics'])
			{
				$this->recent_topics();
			}

			$http_headers = array(
				// application/xhtml+xml not used because of IE
				'Content-type'	=> 'text/html; charset=UTF-8',
				'Cache-Control'	=> 'private, no-cache="set-cookie", pre-check=0, post-check=0, max-age=0',
				'Expires'		=> gmdate('D, d M Y H:i:s', time()) . ' GMT',
				'Pragma'		=> 'no-cache',
			);

			foreach ($http_headers as $hname => $hval)
			{
				header((string) $hname . ': ' . (string) $hval);
			}

			send_status_line(404, 'Not Found');

			$this->template->set_filenames(array(
				'body' => '@bb3mobi_NotFound/notfound_body.html')
			);

			//
			// Output
			//
			$this->template->display('body');

			garbage_collection();
			exit_handler();
		}
	}

	private function recent_topics()
	{
		$forum_ary = array();
		$forum_read_ary = $this->auth->acl_getf('f_read');
		foreach ($forum_read_ary as $forum_id => $allowed)
		{
			if ($allowed['f_read'])
			{
				$forum_ary[] = (int) $forum_id;
			}
		}

		if (sizeof($forum_ary))
		{
			// Recent Topics, Title, Link
			$sql = 'SELECT t.* FROM ' . FORUMS_TABLE . ' f, ' . TOPICS_TABLE . ' t
				WHERE f.forum_id = ' . $forum_id . '
					AND ' . $this->db->sql_in_set('t.forum_id', $forum_ary) . '
				ORDER BY t.topic_last_post_id DESC';

			$result = $this->db->sql_query_limit($sql, $this->config['notfound_recent_limit']);
			$recent_topics = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($recent_topics as $row)
			{
				// Replies
				global $phpbb_container;
				$phpbb_content_visibility = $phpbb_container->get('content.visibility');
				$replies = $phpbb_content_visibility->get_count('topic_posts', $row, $row['forum_id']) - 1;

				$this->template->assign_block_vars('topicrow', array(
					'U_TOPIC'				=> generate_board_url() . '/viewtopic.' . $this->phpEx . '?t=' . $row['topic_id'],
					'TOPIC_TITLE' 			=> $row['topic_title'],
					'TOPIC_AUTHOR_FULL'		=> get_username_string('username', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'FIRST_POST_TIME'		=> $this->user->format_date($row['topic_time']),

					'VIEWS'					=> number_format($row['topic_views']),
					'REPLIES'				=> number_format($replies),
					'U_LAST_POST'			=> generate_board_url() . '/viewtopic.' . $this->phpEx . '?p=' . $row['topic_last_post_id'] . '#p' . $row['topic_last_post_id'],
					'LAST_POST_SUBJECT'		=> censor_text($row['topic_last_post_subject']),
					'LAST_POST_AUTHOR'		=> get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
					'LAST_POST_TIME'		=> $this->user->format_date($row['topic_last_post_time']),
				));
			}
		}
	}
}
