<?php

namespace nekstati\ultimatequiz\migrations;

class v100 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\phpbb\db\migration\data\v320\dev'];
	}

	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'quiz' => [
					'COLUMNS' => [
						'quiz_id'					=> ['UINT', null, 'auto_increment'],
						'quiz_name'					=> ['VCHAR:255', ''],
						'quiz_time'					=> ['TIMESTAMP', 0],
						'quiz_category'				=> ['UINT', 0],
						'user_id'					=> ['ULINT', 0],
						'username'					=> ['VCHAR:255', ''],
						'user_colour'				=> ['VCHAR:10', ''],
						'quiz_time_limit'			=> ['UINT', 0],
					],
					'PRIMARY_KEY' => 'quiz_id',
				],

				$this->table_prefix . 'quiz_categories' => [
					'COLUMNS' => [
						'quiz_category_id'				=> ['UINT', null, 'auto_increment'],
						'quiz_category_name'			=> ['VCHAR:255', ''],
						'quiz_category_dest_group_id'	=> ['UINT', 0],
						'quiz_category_dest_group_pct'	=> ['UINT', 0],
						'quiz_category_description'		=> ['VCHAR:255', ''],
						'quiz_category_group_ids'		=> ['VCHAR:255', 2],
					],
					'PRIMARY_KEY' => 'quiz_category_id',
				],

				$this->table_prefix . 'quiz_questions' => [
					'COLUMNS' => [
						'question_id'				=> ['UINT', null, 'auto_increment'],
						'question_name'				=> ['TEXT', ''],
						'question_correct'			=> ['TEXT', ''],
						'question_answers'			=> ['TEXT', ''],
						'question_quiz'				=> ['UINT', 0],
						'question_bbcode_bitfield'	=> ['VCHAR:255', ''],
						'question_bbcode_uid'		=> ['VCHAR:8', ''],
						'question_bbcode_options'	=> ['UINT', 0],
					],
					'PRIMARY_KEY' => 'question_id',
				],

				$this->table_prefix . 'quiz_sessions' => [
					'COLUMNS' => [
						'quiz_session_id'			=> ['UINT', null, 'auto_increment'],
						'quiz_id'					=> ['UINT', 0],
						'user_id'					=> ['ULINT', 0],
						'started'					=> ['TIMESTAMP', 0],
						'ended'						=> ['TIMESTAMP', 0],
					],
					'PRIMARY_KEY' => 'quiz_session_id',
				],

				$this->table_prefix . 'quiz_statistics' => [
					'COLUMNS' => [
						'quiz_statistic_id'			=> ['UINT', null, 'auto_increment'],
						'quiz_question_id'			=> ['UINT', 0],
						'quiz_user_answer'			=> ['TEXT', ''],
						'quiz_actual_answer'		=> ['TEXT', ''],
						'quiz_is_correct'			=> ['UINT', 0],
						'quiz_user'					=> ['ULINT', 0],
						'quiz_session_id'			=> ['UINT', 0],
					],
					'PRIMARY_KEY' => 'quiz_statistic_id',
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'quiz',
				$this->table_prefix . 'quiz_categories',
				$this->table_prefix . 'quiz_questions',
				$this->table_prefix . 'quiz_sessions',
				$this->table_prefix . 'quiz_statistics',
			],
		];
	}

	public function update_data()
	{
		return [
			['config.add', ['qc_cash_column', 'user_points']],
			['config.add', ['qc_cash_enabled', 0]],
			['config.add', ['qc_admin_submit_only', 0]],
			['config.add', ['qc_quiz_author_edit', 0]],
			['config.add', ['qc_show_answers', 1]],
			['config.add', ['qc_maximum_answers', 5]],
			['config.add', ['qc_maximum_questions', 20]],
			['config.add', ['qc_minimum_questions', 2]],
			['config.add', ['qc_cash_correct', 5]],
			['config.add', ['qc_cash_incorrect', 5]],
			['config.add', ['qc_exclusion_time', 600]],
			['config.add', ['qc_enable_time_limits', 1]],
			['config.add', ['qc_quizzes_on_index', 3]],
			['config.add', ['qc_quizzes_per_page', 10]],

			['module.add', ['acp', 'ACP_CAT_DOT_MODS', 'ACP_UQM_QUIZ_TITLE']],
			['module.add', ['acp', 'ACP_UQM_QUIZ_TITLE', [
				'module_basename'	=> '\nekstati\ultimatequiz\acp\a_module',
				'module_langname'	=> 'ACP_UQM_QUIZ_SETTINGS',
				'module_mode'		=> 'settings',
				'module_auth'		=> 'ext_nekstati/ultimatequiz && acl_a_board',
			]]],
		];
	}
}
