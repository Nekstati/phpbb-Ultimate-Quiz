<?php

namespace nekstati\ultimatequiz\acp;

class a_info
{
	public function module()
	{
		return [
			'filename'	=> '\nekstati\ultimatequiz\acp\a_module',
			'title'		=> 'ACP_UQM_QUIZ_TITLE',
			'modes'		=> [
				'settings'	=> [
					'title'		=> 'ACP_UQM_QUIZ_SETTINGS',
					'auth'		=> 'ext_nekstati/ultimatequiz && acl_a_board',
					'cat'		=> ['ACP_UQM_QUIZ_TITLE'],
				],
			],
		];
	}
}
