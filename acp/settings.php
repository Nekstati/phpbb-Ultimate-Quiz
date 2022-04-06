<?php

namespace nekstati\ultimatequiz\acp;

class settings
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function __construct($u_action, $errors = [])
	{
		global $auth, $cache, $config, $db, $phpbb_container, $phpbb_admin_path, $phpbb_root_path, $table_prefix, $template, $user;

		define('QUIZ_TABLE',			$table_prefix . 'quiz');
		define('QUIZ_QUESTIONS_TABLE',	$table_prefix . 'quiz_questions');
		define('QUIZ_STATISTICS_TABLE',	$table_prefix . 'quiz_statistics');
		define('QUIZ_SESSIONS_TABLE',	$table_prefix . 'quiz_sessions');
		define('QUIZ_CATEGORIES_TABLE',	$table_prefix . 'quiz_categories');

		$this->u_action = $u_action;
		$this->page_title = $user->lang('ACP_UQM_QUIZ_TITLE');

		include $phpbb_root_path . 'includes/functions_user.php';

		$quiz_configuration = new \nekstati\ultimatequiz\core\quiz_configuration;
		$user->add_lang_ext('nekstati/ultimatequiz', 'acp');
		$type = request_var('t', '');

		switch ($type)
		{
			case 'edit_category':

				$category_id = request_var('cat', 0);
				$edit_category_link = $this->u_action . "&amp;t=edit_category&amp;cat=$category_id";

				if (isset($_POST['submit']))
				{
					// Values to update, which we'll pass by reference to the validation checking function.
					$category_name = request_var('category_name', '');
					$group_rewards_destination_group_id = 0;
					$group_rewards_percentage = 0;

					$group_permissions_groups = request_var('permitted_groups', array(0));
					$group_permissions_groups = implode(', ', $group_permissions_groups);

					// This will display an error page if there are failed validations.
					$this->run_category_validations(
						$category_name,
						$group_rewards_percentage,
						$group_rewards_destination_group_id,
						$edit_category_link,
						$category_id
					);

					$category_description = request_var('category_description', '');

					$category_array = array(
						'quiz_category_name'			=> utf8_normalize_nfc($category_name),
						'quiz_category_description'		=> utf8_normalize_nfc($category_description),
						'quiz_category_dest_group_pct'	=> $group_rewards_percentage,
						'quiz_category_dest_group_id'	=> $group_rewards_destination_group_id,
						'quiz_category_group_ids'		=> $group_permissions_groups,
					);

					$sql = '
						UPDATE ' . QUIZ_CATEGORIES_TABLE . '
						SET ' . $db->sql_build_array('UPDATE', $category_array) . '
						WHERE quiz_category_id = ' . $category_id;
					$db->sql_query($sql);

					meta_refresh(2, $this->u_action);
					trigger_error(sprintf($user->lang['ACP_UQM_CATEGORY_UPDATED'], $category_name) . adm_back_link($this->u_action));
				}

				$sql = '
					SELECT *
					FROM ' . QUIZ_CATEGORIES_TABLE . '
					WHERE quiz_category_id = ' . $category_id;
				$result	= $db->sql_query_limit($sql, 1);
				$row = $db->sql_fetchrow($result);

				$category_name			= $row['quiz_category_name'];
				$category_description	= $row['quiz_category_description'];
				$destination_group		= ($row['quiz_category_dest_group_id'] != 0) ? $row['quiz_category_dest_group_id'] : null;
				$group_percentage		= $row['quiz_category_dest_group_pct'];
				$permitted_groups		= explode(', ', $row['quiz_category_group_ids']);

				$db->sql_freeresult($result);

				$template->assign_vars(array(
					'URL_FORM_ACTION'		=> $edit_category_link,
					'CATEGORY_NAME'			=> $category_name,
					'CATEGORY_DESCRIPTION'	=> $category_description,
					'GROUP_REWARDS'			=> (isset($destination_group) && isset($group_percentage)),
					'GROUP_PERCENTAGE'		=> ($group_percentage > 0) ? $group_percentage : null,
					'GROUP_LIST'			=> $this->create_usergroup_list($destination_group),

					// Have the currently chosen groups appear automatically selected for the group permissions
					'MULTI_GROUP_LIST'		=> $this->multi_group_select_options($permitted_groups),

					'EDIT_MODE'				=> true,
				));

				$this->tpl_name = '@nekstati_ultimatequiz/category';

				break;

			case 'add_category':

				$add_category_link = $this->u_action . "&amp;t=add_category";

				if (isset($_POST['submit']))
				{
					// The values we'll be inserting. We'll pass these by reference to the validation function
					// so that they can be assigned values or updated if necessary. For example, there is no need to
					// update the group rewards variables unless the admin has enabled that for this category.
					$category_name = request_var('category_name', '');
					$group_rewards_destination_group_id = 0;
					$group_rewards_percentage = 0;

					$group_permissions_groups = request_var('permitted_groups', array(0));
					$group_permissions_groups = implode(', ', $group_permissions_groups);

					// This will display an error page if there are failed validations.
					$this->run_category_validations(
						$category_name,
						$group_rewards_percentage,
						$group_rewards_destination_group_id,
						$add_category_link
					);

					$category_description = request_var('category_description', '');

					$category_array = array(
						'quiz_category_name'			=> utf8_normalize_nfc($category_name),
						'quiz_category_description'		=> utf8_normalize_nfc($category_description),
						'quiz_category_dest_group_pct'	=> $group_rewards_percentage,
						'quiz_category_dest_group_id'	=> $group_rewards_destination_group_id,
						'quiz_category_group_ids'		=> $group_permissions_groups,
					);

					$sql = 'INSERT INTO ' . QUIZ_CATEGORIES_TABLE . ' ' . $db->sql_build_array('INSERT', $category_array);
					$db->sql_query($sql);

					meta_refresh(2, $this->u_action);
					trigger_error(sprintf($user->lang['ACP_UQM_CATEGORY_ADDED'], $category_name) . adm_back_link($this->u_action));
				}

				$template->assign_vars(array(
					'URL_FORM_ACTION'	=> $add_category_link,
					'CATEGORY_NAME'		=> '',
					'GROUP_REWARDS'		=> false,
					'GROUP_PERCENTAGE'	=> '',
					'GROUP_LIST'		=> $this->create_usergroup_list(),

					// Group permissions, default to registered users when adding a new category
					'MULTI_GROUP_LIST'	=> $this->multi_group_select_options(array(2)),
				));

				$this->tpl_name = '@nekstati_ultimatequiz/category';

				break;

			case 'delete_category':

				$category_id = request_var('cat', 0);

				if (confirm_box(true))
				{
					// Get list of questions in the category
					$sql = '
						SELECT w.question_id
						FROM ' . QUIZ_QUESTIONS_TABLE . ' w, ' . QUIZ_TABLE . ' q
						WHERE w.question_quiz = q.quiz_id
						AND q.quiz_category = ' . $category_id;
					$result = $db->sql_query($sql);
					$question_id_list = array_column($db->sql_fetchrowset($result), 'question_id');
					$db->sql_freeresult($result);

					$delete_sql = array();
					$delete_sql[] = 'DELETE FROM ' . QUIZ_TABLE . '
						WHERE quiz_category = ' . $category_id;

					if (sizeof($question_id_list) > 0)
					{
						$delete_sql[] = 'DELETE FROM ' . QUIZ_QUESTIONS_TABLE . '
							WHERE ' . $db->sql_in_set('question_id', $question_id_list);

						$delete_sql[] = 'DELETE FROM ' . QUIZ_STATISTICS_TABLE . '
							WHERE ' . $db->sql_in_set('quiz_question_id', $question_id_list);
					}

					$delete_sql[] = 'DELETE FROM ' . QUIZ_CATEGORIES_TABLE . '
						WHERE quiz_category_id = ' . $category_id;

					foreach ($delete_sql as $query)
					{
						$db->sql_query($query);
					}

					meta_refresh(2, $this->u_action);
					trigger_error($user->lang['ACP_UQM_DELETE_SUCCESSFUL'] . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, 'ACP_UQM_DELETE_TITLE');
				}

				// No break

			default:

				// If the configuration values have been updated, then do some updating...
				if (isset($_POST['submit']))
				{
					$configuration_list = $quiz_configuration->config_array();
					foreach ($configuration_list as $name)
					{
						// Check the type of setting, and use that as a parameter
						$type = ($quiz_configuration->value($name, true) == 'radio') ? 0 : '';
						$new_value = utf8_normalize_nfc(request_var($name, $type));
						$quiz_configuration->update($name, $new_value);
					}

					$message = $user->lang['ACP_UQM_CONFIG_UPDATED'] . adm_back_link($this->u_action);
					meta_refresh(2, $this->u_action);
					trigger_error($message);
				}

				$this->tpl_name = '@nekstati_ultimatequiz/settings';

				// Get the category list
				$sql = '
					SELECT *
					FROM ' . QUIZ_CATEGORIES_TABLE . '
					ORDER BY quiz_category_name ASC';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('category_row', array(
						'CATEGORY_NAME'		=> $row['quiz_category_name'],
						'EDIT_LINK'			=> $this->u_action . "&amp;t=edit_category&amp;cat={$row['quiz_category_id']}",
						'DELETE_LINK'		=> $this->u_action . "&amp;t=delete_category&amp;cat={$row['quiz_category_id']}",
					));
				}
				$db->sql_freeresult($result);

				$configuration_list = $quiz_configuration->config_array();

				foreach ($configuration_list as $name)
				{
					$lang_name		= $user->lang['ACP_UQM_CONFIG_DEFINITIONS'][$name];
					$lang_explain	= $user->lang['ACP_UQM_CONFIG_DEFINITIONS'][$name . '_explain'];

					$template->assign_block_vars('configuration_row', array(
						'LANG'			=> $lang_name,
						'LANG_EXPLAIN'	=> $lang_explain,
						'NAME'			=> $name,
						'VALUE'			=> $quiz_configuration->value($name),
						'TYPE'			=> $quiz_configuration->value($name, true),
					));
				}

				$template->assign_vars(array(
					'URL_FORM_ACTION'		=> append_sid($this->u_action),
					'ADD_CATEGORY_LINK'		=> sprintf($user->lang['ACP_UQM_ADD_CATEGORY'], '<a href="' . $this->u_action . '&amp;t=add_category">', '</a>'),
				));
		}
	}

	// Do all of the category validation checks (required for both add and edit category pages). Pass most by reference.
	function run_category_validations(&$category_name, &$group_rewards_percentage, &$group_rewards_destination_group_id, $return_link, $category_id = null)
	{
		global $user;

		// Category name validation (required field). We pass the category id in because if the name hasn't changed
		// we don't want it finding a conflict with itself.
		$category_validation = $this->category_validation($category_name, $category_id);

		if ($category_validation !== true)
		{
			// Inform the user that validation failed
			trigger_error($user->lang[$category_validation] . adm_back_link($return_link), E_USER_WARNING);
		}

		// Look at group rewards (these are optional for categories)
		$group_rewards_enabled = request_var('group_rewards_enabled', false);

		if ($group_rewards_enabled)
		{
			$group_rewards_destination_group_id = request_var('group_rewards_group_id', 0);
			$group_rewards_percentage = request_var('group_rewards_percentage', -1);

			// Make sure the user supplied values pass validation
			$group_rewards_validation = $this->group_rewards_validation(
				$group_rewards_percentage,
				$group_rewards_destination_group_id
			);

			if ($group_rewards_validation !== true)
			{
				// Inform the user that validation failed
				trigger_error($user->lang[$group_rewards_validation] . adm_back_link($return_link), E_USER_WARNING);
			}
		}
	}

	// Check that the category passes validation
	function category_validation($category_name, $category_id = null)
	{
		global $db;

		// Category name is empty
		if (strlen($category_name) < 1)
		{
			return 'ACP_UQM_CATEGORY_NAME_VALIDATE';
		}
		else
		{
			$filtered_category_name = $db->sql_escape(utf8_normalize_nfc($category_name));

			$sql = '
				SELECT COUNT(quiz_category_id) AS count_names
				FROM ' . QUIZ_CATEGORIES_TABLE . "
				WHERE quiz_category_name = '$filtered_category_name'";
			$sql .= (isset($category_id)) ? ' AND quiz_category_id != ' . (int) $category_id : '';
			$result = $db->sql_query($sql);
			$category_count = $db->sql_fetchfield('count_names');
			$db->sql_freeresult($result);

			// Category name already exists
			if ($category_count > 0)
			{
				return 'ACP_UQM_CATEGORY_NAME_VALIDATE';
			}
		}

		// Passed validation, everything is fine
		return true;
	}

	// Check that the percentage is a valid number and the usergroup exists
	function group_rewards_validation($percentage, $group_id)
	{
		global $db;

		$passed = false;

		$percentage_valid = ($percentage >= 0 && $percentage <= 100);

		// Percentage is invalid
		if (!$percentage_valid)
		{
			return 'ACP_UQM_CATEGORY_GROUP_REWARDS_PERCENTAGE_VALIDATE';
		}

		// $usergroup_exists = ($group_id > 7 && (get_group_name($group_id) != ''));
		$usergroup_exists = (get_group_name($group_id) != '');

		// Usergroup is invalid
		if (!$usergroup_exists)
		{
			return 'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP_VALIDATE';
		}

		// Everything is fine
		return true;
	}

	// Make the dropdown menu of usergroups that a user could be moved to
	function create_usergroup_list($default_group_id = null)
	{
		global $db, $user;

		$sql = '
			SELECT group_id, group_name
			FROM ' . GROUPS_TABLE . '
			WHERE group_type <> ' . GROUP_SPECIAL;

		$result = $db->sql_query($sql);

		$select = (isset($default_group_id)) ? '<select name="group_rewards_group_id">' : '<select name="group_rewards_group_id" disabled="true">';
		$select .= '	<option value="0">' . $user->lang['ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP_SELECT'] . '</option>';

		// Iterate through the groups
		while ($row = $db->sql_fetchrow($result))
		{
			$selected = (isset($default_group_id) && $row['group_id'] == (int) $default_group_id) ? ' selected="selected"' : '';
			$select .= '	<option value="' . $row['group_id'] . '"' . $selected . '>' . $row['group_name'] . '</option>';
		}
		$db->sql_freeresult($result);

		$select .= '</select>';

		return $select;
	}

	// Create a list of <select> options with all available groups.
	// This function keeps multiple groups selected if an array with ids is provided.
	function multi_group_select_options($group_ids)
	{
		global $db, $user;

		$group_ids = (is_array($group_ids)) ? $group_ids : array($group_ids);
		$sql_where = ($user->data['user_type'] == USER_FOUNDER) ? '' : 'WHERE group_founder_manage = 0';

		$sql = 'SELECT group_id, group_name, group_type
			FROM ' . GROUPS_TABLE . "
			$sql_where
			ORDER BY group_type DESC, group_name ASC";
		$result = $db->sql_query($sql);

		$s_group_options = '';

		while ($row = $db->sql_fetchrow($result))
		{
			$selected = (is_array($group_ids) && in_array($row['group_id'], $group_ids)) ? ' selected="selected"' : '';
			$s_group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];
			$s_group_options .= '<option'  . (($row['group_type'] == GROUP_SPECIAL) ? ' class="sep"' : '') . ' value="' . $row['group_id'] . '"' . $selected . '>' . $s_group_name . '</option>';
		}
		$db->sql_freeresult($result);

		return $s_group_options;
	}
}
