<?php

namespace nekstati\ultimatequiz\core;

class quiz_configuration
{
	var $qc_config_array;
	var $qc_config_value;
	var $debug = false;

	// Which categories this user is allowed to view
	var $qc_user_viewable_categories = array();

	function __construct()
	{
		global $config, $user;

		$this->router = $GLOBALS['phpbb_container']->get('routing.helper');

		$this->qc_user_viewable_categories = $this->get_categories_user_can_access($user->data['user_id']);

		// Load in configuration fields from config table
		$this->qc_config_array = array(
			'qc_minimum_questions',			// minimum number of questions allowed
			'qc_maximum_questions',			// maximum number of questions allowed
			'qc_maximum_answers',			// maximum number of answers per question
			'qc_show_answers',				// show the answers after a quiz
			'qc_quiz_author_edit',			// allow the author to edit their own quiz
			'qc_admin_submit_only',			// only administrators are allowed to submit quizzes
			'qc_enable_time_limits',		// are time limits on or off
			'qc_exclusion_time',			// exclusion time (seconds) if a user does not finish a quiz or exceeds the time limit
			'qc_quizzes_per_page',			// how many quizzes per page in the category view
			'qc_quizzes_on_index',			// how many recent quizzes from each category should be shown on the index
			'qc_cash_enabled',				// enable cash functionality
			'qc_cash_column',				// associated column in the users table
			'qc_cash_correct',				// cash gained for a correct answer
			'qc_cash_incorrect',			// cash deducted for an incorect answer
		);

		// Get their values. We'll define the keys and values separately in case we need to
		// manipulate the values at some point in the future.
		$this->qc_config_value = array(
			'qc_minimum_questions'		=> $this->generate_config_array('qc_minimum_questions', 'number'),
			'qc_maximum_questions'		=> $this->generate_config_array('qc_maximum_questions', 'number'),
			'qc_maximum_answers'		=> $this->generate_config_array('qc_maximum_answers', 'number'),

			'qc_show_answers'			=> $this->generate_config_array('qc_show_answers', 'radio'),
			'qc_quiz_author_edit'		=> $this->generate_config_array('qc_quiz_author_edit', 'radio'),
			'qc_admin_submit_only'		=> $this->generate_config_array('qc_admin_submit_only', 'radio'),
			'qc_enable_time_limits'		=> $this->generate_config_array('qc_enable_time_limits', 'radio'),
			'qc_exclusion_time'			=> $this->generate_config_array('qc_exclusion_time', 'number'),

			'qc_quizzes_per_page'		=> $this->generate_config_array('qc_quizzes_per_page', 'number'),
			'qc_quizzes_on_index'		=> $this->generate_config_array('qc_quizzes_on_index', 'number'),

			'qc_cash_enabled'			=> $this->generate_config_array('qc_cash_enabled', 'radio'),
			'qc_cash_column'			=> $this->generate_config_array('qc_cash_column', 'text'),

			'qc_cash_correct'			=> $this->generate_config_array('qc_cash_correct', 'number'),
			'qc_cash_incorrect'			=> $this->generate_config_array('qc_cash_incorrect', 'number'),
		);
	}

	// Rather than creating an array each time we can use this function to make it for us
	function generate_config_array($configuration_name, $input_type)
	{
		global $config;

		return array('value' => (isset($config[$configuration_name])) ? $config[$configuration_name] : ($input_type == 'text' ? '' : 0), 'type' => $input_type);
	}

	// Return this list of config values
	function config_array()
	{
		return $this->qc_config_array;
	}

	// Update configuration values
	function update($name, $value)
	{
		if (array_search($name, $this->qc_config_array) !== false)
		{
			set_config($name, $value);
		}
	}

	// Check if cash functionality is enabled and if so update the users table accordingly
	// depending on the results the user got in the quiz
	function cash($correct, $incorrect)
	{
		global $user, $db, $template;

		// Only do anything if cash is enabled
		if ($this->value('qc_cash_enabled') && !empty($this->value('qc_cash_column'))
		&& ($this->value('qc_cash_correct') > 0 || $this->value('qc_cash_incorrect') > 0))
		{
			$cash_column = $db->sql_escape($this->value('qc_cash_column'));

			$cash_earned = ($correct * $this->value('qc_cash_correct'));
			$cash_squandered = ($incorrect * $this->value('qc_cash_incorrect'));

			$difference = $cash_earned - $cash_squandered;

			// Don't show fatal SQL error if $cash_column doesn't exist or its value is out of bounds
			$db->sql_return_on_error(true);

			$sql = '
				UPDATE ' . USERS_TABLE . '
				SET ' . $cash_column . ' = ' . $cash_column . ' + ' . $difference . '
				WHERE user_id = ' . (int) $user->data['user_id'];
			$db->sql_query($sql);

			if ($db->get_sql_error_triggered())
			{
				if ($this->debug)
				{
					$error = 'SQL ERROR [' . $db->get_sql_layer() . ']: ' . $db->get_sql_error_returned()['message'] . ' [' . $db->get_sql_error_returned()['code'] . ']';
					$template->assign_var('QUIZ_CASH_ERROR', $user->lang('UQM_CASH_ERROR', $error));
				}
			}
			else
			{
				$cash_message = ($difference < 0)
					? $user->lang('UQM_QUIZ_CASH_LOST', abs($difference), $user->lang('UQM_POINTS', abs($difference)))
					: $user->lang('UQM_QUIZ_CASH_GAIN', $difference, $user->lang('UQM_POINTS', $difference));
				$template->assign_var('QUIZ_CASH_MESSAGE', $cash_message);
			}

			$db->sql_return_on_error(false);
		}
	}

	// Group rewards: if the admin has specified, then if a user completes all quizzes in a category to a certain
	// accuracy level they will be moved into a usergroup. If they are moved into a usergroup, a message will be returned.
	function group_rewards($quiz_id)
	{
		global $db, $user, $phpbb_root_path;

		// Include this for the usergroup functions
		include_once($phpbb_root_path . 'includes/functions_user.php');

		// Get the basic quiz and category details
		$sql = '
			SELECT c.*
			FROM ' . QUIZ_CATEGORIES_TABLE . ' c, ' . QUIZ_TABLE . ' q
			WHERE q.quiz_category = c.quiz_category_id
			AND q.quiz_id = ' . (int) $quiz_id;

		$result	= $db->sql_query($sql);
		$row	= $db->sql_fetchrow($result);

		// Get the data
		$quiz_category_id							= $row['quiz_category_id'];
		$quiz_category_dest_group_id		= $row['quiz_category_dest_group_id'];
		$quiz_category_dest_group_pct	= $row['quiz_category_dest_group_pct'];

		$db->sql_freeresult($result);

		// If either are null, then there is no group rewards for this category
		if (isset($quiz_category_dest_group_id) && isset($quiz_category_dest_group_pct))
		{
			// The first thing to do is see if the user is a member of the usergroup. Because if they are, there is no
			// point continuing...
			if (group_memberships($quiz_category_dest_group_id, $user->data['user_id'], true))
			{
				return;
			}

			// Get the statistics data
			$sql = '
				SELECT s.*, q.question_quiz
				FROM ' . QUIZ_STATISTICS_TABLE . ' s, ' . QUIZ_QUESTIONS_TABLE . ' q, ' . QUIZ_TABLE . ' t
				WHERE s.quiz_user = ' . $user->data['user_id'] . '
					AND q.question_id = s.quiz_question_id
					AND t.quiz_id = q.question_quiz
					AND t.quiz_category = ' . $quiz_category_id . '
				ORDER BY q.question_quiz';
			$result = $db->sql_query($sql);

			// Raw statistics information for a user
			$statistics_array = array();

			// Keep a record of the quiz ids played by the user
			$quiz_id_array	= array();
			$quiz_scores	= array();

			while ($row = $db->sql_fetchrow($result))
			{
				$is_correct = false;

				if ($row['quiz_is_correct'] > 0)
				{
					$is_correct = true;
				}

				$statistics_array[$row['quiz_session_id']][] = array(
					'quiz_id'			=> $row['question_quiz'],
					'question_id'		=> $row['quiz_question_id'],
					'quiz_session_id'	=> $row['quiz_session_id'],
					'is_correct'		=> (bool) $is_correct
				);

				// Add the played quiz id to the array if it's not already there
				if (!in_array($row['question_quiz'], $quiz_id_array))
				{
					$quiz_id_array[] = $row['question_quiz'];

					// We'll start this array now. Essentially, it will keep a record of the top percentage
					// a user has for that quiz.
					$quiz_scores[$row['question_quiz']] = 0;
				}
			}
			$db->sql_freeresult($result);

			// First check that the user has completed all quizzes in the category (by doing a NOT IN).
			$sql = '
				SELECT COUNT(quiz_id) AS unplayed
				FROM ' . QUIZ_TABLE . '
				WHERE ' . $db->sql_in_set('quiz_id', $quiz_id_array, true) . '
					AND quiz_category = ' . $quiz_category_id;

			$result = $db->sql_query($sql);

			// We will only proceed beyond this point if the user has played all of the quizzes in the category
			if ($db->sql_fetchfield('unplayed') > 0)
			{
				return;
			}

			$db->sql_freeresult($result);

			// The unique keys are also a list of session ids for that user
			// We just need to see if they scored the minimum percentage for each quiz.
			$sessions_played = array_keys($statistics_array);

			// Now check the results; each $quiz_played is a quiz_id
			foreach ($sessions_played as $quiz_session)
			{
				$correct_answers	= 0;
				$incorrect_answers	= 0;
				$session_quiz_id	= 0;

				// Loop through each question in the statistics array for this session
				foreach ($statistics_array[$quiz_session] as $statistics_item)
				{
					$session_quiz_id = ($session_quiz_id == 0) ? $statistics_item['quiz_id'] : $session_quiz_id;

					if ($statistics_item['is_correct'])
					{
						// The user got this question correct
						$correct_answers++;
					}
					else
					{
						// The user got this question incorrect
						$incorrect_answers++;
					}
				}

				// The percentage the user got for this quiz in this session
				$session_percentage = 100 * $correct_answers / ($correct_answers + $incorrect_answers);

				// If this is the highest percentage so far for a user in this quiz, we'll update this variable.
				$quiz_scores[$session_quiz_id] = ($session_percentage > $quiz_scores[$session_quiz_id]) ? $session_percentage : $quiz_scores[$session_quiz_id];
			}

			// Let's look at all of the top quiz scores by this user, now that we have them
			foreach ($quiz_scores as $score)
			{
				if ($score < $quiz_category_dest_group_pct)
				{
					// Any score below the threshold means there is no point continuing
					return;
				}
			}

			// If we've reached this point, then we can move the user to the usergroup!
			group_user_add($quiz_category_dest_group_id, $user->data['user_id']);

			return sprintf($user->lang['UQM_RESULTS_GROUP_REWARD'], $quiz_category_dest_group_pct, get_group_name($quiz_category_dest_group_id));
		}
	}

	// Return the configuration value for "$setting"
	function value($setting, $type = false)
	{
		$configuration_value = false;

		if (array_search($setting, $this->qc_config_array) === false)
		{
			trigger_error('UQM_QUIZ_CONFIG_ERROR');
		}
		else
		{
			// Return the type of setting (ie. input, radio for something the user enters or
			// clicks yes/no respectively) if type is defined.
			$array_element = ($type) ? 'type' : 'value';
			$configuration_value = $this->qc_config_value[$setting][$array_element];
		}

		return $configuration_value;
	}

	// Handle the breadcrumbs (navigational links) for each quiz page
	function add_breadcrumbs($links)
	{
		global $template;

		foreach ($links as $name => $link)
		{
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $name,
				'U_VIEW_FORUM'	=> $link,
			));
		}
	}

	// Check to see whether the "new" question value would fall outside
	// of the boundaries of minimum and maximum questions and return false
	// if this is the case
	function check_question_boundaries($questions, $alteration)
	{
		$follow = true;

		$less_than_minimum = (($questions + $alteration) < $this->value('qc_minimum_questions'));
		$greater_than_maximum = (($questions + $alteration) > $this->value('qc_maximum_questions'));

		if ($less_than_minimum || $greater_than_maximum)
		{
			$follow = false;
		}

		return $follow;
	}

	// Ensure that every question has an associated correct answer
	function check_correct_checked($question_number)
	{
		$empty = true;

		for ($i = 0; $i < $question_number; $i++)
		{
			$answer_given = request_var("answer_$i", -1);

			if ($answer_given < 0)
			{
				$empty = false;
				break;
			}
		}

		return $empty;
	}

	function gen_categories_options($default_id = 0, $restrict_by_access = false)
	{
		global $db;

		$sql = 'SELECT * FROM ' . QUIZ_CATEGORIES_TABLE;

		if ($restrict_by_access)
		{
			// Don't continue if no categories are available
			if (sizeof($this->qc_user_viewable_categories) == 0)
			{
				trigger_error('UQM_CATEGORIES_NOT_AVAILABLE');
			}

			// If we are restricting by access, that means we don't want to show any categories that this
			// user can't access.
			$sql .= ' WHERE ' . $db->sql_in_set('quiz_category_id', $this->qc_user_viewable_categories);
		}

		$result = $db->sql_query($sql);

		$html = '';

		while ($row = $db->sql_fetchrow($result))
		{
			// If a default id is given, then ensure that it is selected by default
			$selected = ($row['quiz_category_id'] == $default_id) ? ' selected="selected"' : '';
			$html .= '	<option value="' . $row['quiz_category_id'] . '"' . $selected . '>' . $row['quiz_category_name'] . '</option>';
		}
		$db->sql_freeresult($result);

		return $html;
	}

	// Determine quiz percentage
	function determine_percentage($numerator, $partial_denominator)
	{
		$denominator = ($numerator + $partial_denominator);

		$multiply = (!is_int($denominator) || $denominator == 0 || $denominator == null) ? 0 : ((100 * $numerator) / $denominator);
		$format = number_format($multiply, 0);

		return $format;
	}

	function get_quiz($quiz_id)
	{
		global $db;

		$sql = '
			SELECT *
			FROM ' . QUIZ_TABLE . '
			WHERE quiz_id = ' . (int) $quiz_id;
		$result = $db->sql_query_limit($sql, 1);
		$quiz = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $quiz;
	}

	function get_quiz_questions_ids($quiz_id)
	{
		global $db;

		$sql = '
			SELECT question_id
			FROM ' . QUIZ_QUESTIONS_TABLE . '
			WHERE question_quiz = ' . (int) $quiz_id;
		$result = $db->sql_query($sql);
		$question_id_array = array_column($db->sql_fetchrowset($result), 'question_id');
		$db->sql_freeresult($result);

		return $question_id_array;
	}

	// "Edit", "Delete" and "Statistics" links
	function gen_quiz_extra_links($auth_params)
	{
		global $user;

		$string = array();

		if( $this->auth('stats', $auth_params) )
		{
			$string[] = sprintf($user->lang['UQM_INDEX_STATS'], '<a href="' . $this->router->route('nekstati_quiz_index', ['mode' => 'stats', 'q' => $auth_params['quiz_information']['quiz_id']]) . '">', '</a>');
		}

		if( $this->auth('edit', $auth_params) )
		{
			$string[] = sprintf($user->lang['UQM_INDEX_EDIT'], '<a href="' . $this->router->route('nekstati_quiz_index', ['mode' => 'edit', 'q' => $auth_params['quiz_information']['quiz_id']]) . '">', '</a>');
		}

		return implode(" | ", $string);
	}

	// Return the list of categories a user can access
	function get_categories_user_can_access($user_id)
	{
		global $db, $user, $phpbb_root_path;

		// Include this for the usergroup functions
		include_once($phpbb_root_path . 'includes/functions_user.php');

		// This is what will get returned
		$acceptable_categories = array();

		$sql = '
			SELECT quiz_category_id, quiz_category_group_ids
			FROM ' . QUIZ_CATEGORIES_TABLE;
		$result = $db->sql_query($sql);

		// Iterate through each category and check if the specified user is a member of any of the usergroups,
		// if so then add that category to the list of acceptable categories.
		while ($row = $db->sql_fetchrow($result))
		{
			$group_id_array = explode(",", $row['quiz_category_group_ids']);
			$is_user_in_group = group_memberships($group_id_array, $user_id, true);

			if ($is_user_in_group)
			{
				// The user is in the group
				$acceptable_categories[] = (int) $row['quiz_category_id'];
			}
		}
		$db->sql_freeresult($result);

		return $acceptable_categories;
	}

	// Authentication for certain pages: 'stats', 'edit', 'add', 'play'
	function auth($case, $parameters = false)
	{
		// if the "return_value" parameter is true, then we want to return a true or false value
		// from the function for whether a user does (true) or does not (false) have permissions
		// to the particular case.
		$can_view = true;

		if (isset($parameters['quiz_information']))
		{
			// If we have quiz information, we can do a catch all check on whether the category is accessible
			$quiz_category_id = (int) $parameters['quiz_information']['quiz_category'];
			$category_accessible = in_array($quiz_category_id, $this->qc_user_viewable_categories);

			if (!$category_accessible)
			{
				if ($parameters['return_value'])
					$can_view = false;
				else
					trigger_error('UQM_CATEGORY_QUIZ_NO_PERMISSION');
			}

		}

		switch ($case)
		{
			// Parameters passed in: administrator, qc_admin_submit_only configuration setting
			case 'add':

				if ($parameters['submit_setting'] && !$parameters['administrator'])
				{
					if ($parameters['return_value'])
						$can_view = false;
					else
						trigger_error('UQM_SUBMIT_NO_PERMISSIONS');
				}

				break;

			// Parameters passed in: administrator, user_id, quiz_information, played_quiz
			case 'stats':

				// Is this user the author
				$is_author = ($parameters['quiz_information']['user_id'] == $parameters['user_id']);

				if (!$parameters['administrator'] && !$parameters['played_quiz'] && !$is_author)
				{
					// If the user is not an administrator, the quiz author or played the quiz - error
					if ($parameters['return_value'])
						$can_view = false;
					else
						trigger_error('UQM_STATS_CANNOT_VIEW');
				}

				break;

			// Play permissions
			case 'play':

				// TODO: any additional play permissions. Basically a placeholder for later
				break;

			// Parameters passed in: administrator, user_id, quiz_information
			case 'edit':

				// Only worry about checking if the user is not an administrator
				if (!$parameters['administrator'])
				{
					// Is this non-admin user the quiz author?
					$is_author = ($parameters['quiz_information']['user_id'] == $parameters['user_id']);

					// Can users edit their own quizzes?
					if (!($this->value('qc_quiz_author_edit') && $is_author))
					{
						// Through the negation, we know that the user is not the author
						// The only way this if statement will NOT be accessed is if both the config
						// setting and $is_author are true. If either of them are false, the user
						// don't have the required permissions to edit.

						if ($parameters['return_value'])
							$can_view = false;
						else
							trigger_error('UQM_EDIT_NOT_ALLOWED');
					}
				}
				break;
		}

		return $can_view;
	}
}
