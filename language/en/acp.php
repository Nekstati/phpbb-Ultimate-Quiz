<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_UQM_QUIZ_SETTINGS'			=> 'Ultimate Quiz — settings',
	'ACP_UQM_QUIZ_CATEGORIES'		=> 'Categories',
	'ACP_UQM_QUIZ_CONFIGURATION'	=> 'Configuration',
	'ACP_UQM_CATEGORY_EDIT'			=> 'Ultimate Quiz — edit category',
	'ACP_UQM_CATEGORY_ADD'			=> 'Ultimate Quiz — add category',

	'ACP_UQM_EDIT_CATEGORY'			=> 'Edit category',
	'ACP_UQM_DELETE_CATEGORY'		=> 'Delete category',
	'ACP_UQM_ADD_CATEGORY'			=> '%s Add new category %s',

	'ACP_UQM_CATEGORY_INFO'									=> 'Name and description',
	'ACP_UQM_CATEGORY_NAME'									=> 'Enter the category name',
	'ACP_UQM_CATEGORY_NAME_EXPLAIN'							=> 'You must enter a name for this category.',
	'ACP_UQM_CATEGORY_DESCRIPTION'							=> 'Enter the category description',
	'ACP_UQM_CATEGORY_DESCRIPTION_EXPLAIN'					=> 'Leave blank for no description.',
	'ACP_UQM_CATEGORY_NAME_VALIDATE'						=> 'Quiz category names cannot be empty or match an existing quiz category name.',
	'ACP_UQM_CATEGORY_GROUP_REWARDS'						=> 'Group rewards',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_EXPLAIN'				=> 'If a user completes each of the quizzes (at a given point in time) to at least the percentage specified, they can be moved to a specified usergroup. Do you want to enable this feature for this category?',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP'					=> 'Destination usergroup',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP_SELECT'			=> 'Select a usergroup...',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP_EXPLAIN'			=> 'You can only select from groups created by administrator, not from the default (preinstalled) groups.',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP_VALIDATE'			=> 'The usergroup must already exist and must not be a default usergroup.',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_PERCENTAGE'				=> 'Minimum percentage',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_PERCENTAGE_EXPLAIN'		=> 'The user must achieve this percentage as a minimum in each of the quizzes in the category.',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_PERCENTAGE_VALIDATE'	=> 'The minimum percentage must be a whole number between 0 and 100.',
	'ACP_UQM_CATEGORY_ADDED'								=> 'The category <strong>%s</strong> has been successfully added.',
	'ACP_UQM_CATEGORY_UPDATED'								=> 'The category <strong>%s</strong> has been successfully updated.',
	'ACP_UQM_CATEGORY_GROUP_PERMISSIONS'					=> 'Quiz group permissions',
	'ACP_UQM_CATEGORY_GROUP_PERMISSIONS_GROUP'				=> 'Permitted groups',
	'ACP_UQM_CATEGORY_GROUP_PERMISSIONS_GROUP_EXPLAIN'		=> 'Select the groups that should be able to play quizzes within this category.',

	'ACP_UQM_DELETE_TITLE'			=> 'Ultimate Quiz — delete category',
	'ACP_UQM_DELETE_TITLE_CONFIRM'	=> 'Are you sure you wish to delete this category and all of the quizzes inside it?',

	'ACP_UQM_DELETE_SUCCESSFUL'		=> 'The quiz category (and all quizzes inside it) has been deleted.',

	'ACP_UQM_CONFIG_UPDATED'		=> 'The quiz configuration settings have been successfully updated.',

	'ACP_UQM_CONFIG_DEFINITIONS'	=> array(
		'qc_minimum_questions'			=> 'Minimum number of questions',
		'qc_minimum_questions_explain'	=> 'What is the minimum number of questions permitted per quiz?',
		'qc_maximum_questions'			=> 'Maximum number of questions',
		'qc_maximum_questions_explain'	=> 'What is the maximum number of questions permitted per quiz?',
		'qc_maximum_answers'			=> 'Maximum answer options per one question',
		'qc_maximum_answers_explain'	=> 'What is the maximum number of answer options permitted for a question?',
		'qc_show_answers'				=> 'Show quiz answers',
		'qc_show_answers_explain'		=> 'Should the correct answers be shown to the user once they have completed the quiz?',
		'qc_quiz_author_edit'			=> 'Allow users to edit own quizzes',
		'qc_quiz_author_edit_explain'	=> 'Can quiz authors edit and delete their own quizzes? If no, only administrators can.',
		'qc_admin_submit_only'			=> 'Allow only admin to add new quizzes',
		'qc_admin_submit_only_explain'	=> 'Should administrators be the only users permitted to submit quizzes?',
		'qc_enable_time_limits'			=> 'Enable time limits',
		'qc_enable_time_limits_explain'	=> 'If time limits are enabled then quiz submitters can specify the maximum amount of time allowed for users to complete the quiz.',
		'qc_exclusion_time'				=> 'Exclusion time',
		'qc_exclusion_time_explain'		=> 'If a user does not finish a quiz or exceeds the time limit, how many seconds do they need to wait until they can play the quiz again? Enter 0 to disable this restriction.',
		'qc_quizzes_per_page'			=> 'Quizzes per page',
		'qc_quizzes_per_page_explain'	=> 'Specify the number of quizzes you would like to appear per page in each category.',
		'qc_quizzes_on_index'			=> 'Quizzes per category on index page',
		'qc_quizzes_on_index_explain'	=> 'Specify the number of quizzes from each category you would like displayed on the quiz index.',
		'qc_cash_enabled'				=> 'Enable cash/points integration',
		'qc_cash_enabled_explain'		=> 'If you have installed a cash/points extension, you can integrate it with the Ultimate Quiz. Do not enable this if you don’t know exactly what you do.',
		'qc_cash_column'				=> 'Cash/points column',
		'qc_cash_column_explain'		=> 'Name of the column in the users table associated with cash or points.',
		'qc_cash_correct'				=> 'Cash/points for correct answers',
		'qc_cash_correct_explain'		=> 'How many cash/points should be awarded for each correct answer?',
		'qc_cash_incorrect'				=> 'Cash/points lost for incorrect answers',
		'qc_cash_incorrect_explain'		=> 'How many cash/points should be deducted for each wrong answer?',
	),
));
