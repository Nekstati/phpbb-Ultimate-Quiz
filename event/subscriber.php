<?php

namespace nekstati\ultimatequiz\event;

class subscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		if (defined('ADMIN_START'))
		{
			return [];
		}

		return [
			'core.user_setup'						=> 'user_setup',
			'core.page_header'						=> 'add_page_header_link',
		];
	}

	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\routing\helper */
	protected $router;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $table_prefix;

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\controller\helper $helper,
		\phpbb\request\request $request,
		\phpbb\routing\helper $router,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$root_path,
		$table_prefix
	)
	{
		$this->auth			= $auth;
		$this->config		= $config;
		$this->db			= $db;
		$this->language 	= $language;
		$this->helper   	= $helper;
		$this->request  	= $request;
		$this->router   	= $router;
		$this->template 	= $template;
		$this->user 		= $user;
		$this->root_path  	= $root_path;
		$this->table_prefix	= $table_prefix;
	}

	public function user_setup($event)
	{
		$event['lang_set_ext'] = array_merge($event['lang_set_ext'], [[
			'ext_name'		=> 'nekstati/ultimatequiz',
			'lang_set'		=> 'common',
		]]);
	}

	public function add_page_header_link($event)
	{
		$this->template->assign_vars([
			'U_QUIZ'		=> $this->router->route('nekstati_quiz_index'),
		]);
	}
}
