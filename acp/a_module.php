<?php

namespace nekstati\ultimatequiz\acp;

class a_module
{
	var $u_action;
	var $tpl_name;
	var $page_title;

	function main($id, $mode)
	{
		global $phpbb_container;
		$request = $phpbb_container->get('request');
		$user = $phpbb_container->get('user');
		$errors = [];

		add_form_key('uqm_acp');

		if ($request->is_set_post('submit') && !check_form_key('uqm_acp'))
		{
			$errors[] = $user->lang['FORM_INVALID'];
		}

		switch ($mode)
		{
			case 'settings':
				$class = __NAMESPACE__ . '\\' . $mode;
				$instance = new $class($this->u_action, $errors);
				$this->tpl_name = $instance->tpl_name;
				$this->page_title = $instance->page_title;
			break;

			default:
				trigger_error('NO_MODE');
		}
	}
}
