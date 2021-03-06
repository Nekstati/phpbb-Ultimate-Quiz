<?php

namespace nekstati\ultimatequiz\core;

class quiz_statistics
{
	var $question_ids;
	var $quiz_id;

	function initialise($quiz_id)
	{
		global $db, $template;

		$this->quiz_id = (int) $quiz_id;
	
		$sql = '
			SELECT DISTINCT(question_id)
			FROM ' . QUIZ_QUESTIONS_TABLE . '
			WHERE question_quiz = ' . (int) $quiz_id;
		$result = $db->sql_query($sql);
		$this->question_ids = array_column($db->sql_fetchrowset($result), 'question_id');
		$db->sql_freeresult($result);

		// Initialise the statistics we want to display
		$template->assign_vars( array(
			'AVERAGE_SCORES'	=> true,
			'QUESTION_SUMMARY'	=> true,
			'SURVEY'			=> true,
		));
	}

	// Output the summary for the quiz, ie. number of plays, percentage correct, percentage incorrect, average score etc
	function average_scores()
	{
		global $db, $template, $quiz_configuration, $quiz_information;

		$sql_array = array(
			'SELECT'	=> 'COUNT(s.quiz_question_id) AS plays, SUM(s.quiz_is_correct) AS correct',
			'FROM'		=> array(QUIZ_STATISTICS_TABLE => 's'),
			'WHERE'		=> $db->sql_in_set('s.quiz_question_id', $this->question_ids),
		);

		$sql 	= $db->sql_build_query('SELECT', $sql_array);
		$result	= $db->sql_query($sql);
		$row 	= $db->sql_fetchrow($result);

		// Divide plays by size of the question id array to determine how many times the actual 
		// quiz has been played.
		$stats = array();
		$stats['QUIZ_NAME']				= $quiz_information['quiz_name'];
		$stats['NUMBER_OF_QUESTIONS'] 	= (int) sizeof($this->question_ids);
		$stats['TIMES_PLAYED']			= (int) ($row['plays'] / $stats['NUMBER_OF_QUESTIONS']);
		$stats['CORRECT_ANSWERS']		= (int) $row['correct'];
		$stats['INCORRECT_ANSWERS']		= (int) ($row['plays'] - $stats['CORRECT_ANSWERS']);
		$stats['CORRECT_PERCENT']		= (int) $quiz_configuration->determine_percentage($stats['CORRECT_ANSWERS'], $stats['INCORRECT_ANSWERS']);
		$stats['INCORRECT_PERCENT']		= (!$stats['TIMES_PLAYED']) ? 0 : (int) (100 - $stats['CORRECT_PERCENT']); 
		$stats['AVERAGE_SCORE']			= (!$stats['TIMES_PLAYED']) ? '--' : number_format($stats['NUMBER_OF_QUESTIONS'] * ($stats['CORRECT_PERCENT'] / 100), 2);

		$template->assign_vars($stats);
		$db->sql_freeresult($result);
	}

	// Survey's - show a rundown of how users have submitted answers. I think users will like this module!
	function survey()
	{
		global $user, $db, $template, $quiz_configuration;

		$questions = $ids = $answers = $totals = array();

		$sql = '
			SELECT q.question_name, q.question_bbcode_bitfield, q.question_bbcode_uid,
				q.question_bbcode_options, s.quiz_question_id, s.quiz_user_answer, 
				COUNT(s.quiz_user_answer) AS selected	
			FROM ' . QUIZ_STATISTICS_TABLE . ' s, ' . QUIZ_QUESTIONS_TABLE . ' q
			WHERE ' . $db->sql_in_set('s.quiz_question_id', $this->question_ids) . '
				AND s.quiz_question_id = q.question_id
			GROUP BY s.quiz_question_id, q.question_name, q.question_bbcode_bitfield, q.question_bbcode_uid, q.question_bbcode_options, s.quiz_user_answer';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$row_id = $row['quiz_question_id'];

			// Store the question ids as unique identifiers, and the questions, answers and 
			// answer count in the arrays
			$ids[] = $row_id;

			$questions[$row_id] = array(
				'question_name'				=> $row['question_name'],
				'question_bbcode_bitfield'	=> $row['question_bbcode_bitfield'],
				'question_bbcode_uid'		=> $row['question_bbcode_uid'],
				'question_bbcode_options'	=> $row['question_bbcode_options'],
			);

			$answers[$row_id][] = array($row['quiz_user_answer'] => $row['selected']);

			// It should be equal to the play count for each, but it's good to be certain
			(empty($totals[$row_id])) ? $totals[$row_id] = $row['selected'] : $totals[$row_id] += $row['selected'];

			unset($row_id);
		}
		$db->sql_freeresult($result);

		$unique_ids = array_unique($ids);
	
		foreach ($unique_ids as $row_id)
		{
			// Question name, which is important as we are looping around the questions	
			$question_name = generate_text_for_display($questions[$row_id]['question_name'], $questions[$row_id]['question_bbcode_uid'], $questions[$row_id]['question_bbcode_bitfield'], $questions[$row_id]['question_bbcode_options']);

			$template->assign_block_vars('question_row', array(
				'QUESTION_NAME'	=> $question_name,
			));

			for ($i = 0; $i < sizeof($answers[$row_id]); $i++)
			{
				// Now we have the answer as well as how many times it was selected
				foreach ($answers[$row_id][$i] as $answer_name => $answer_selected)
				{
					$percent = 100 * ($answer_selected / $totals[$row_id]);

					$template->assign_block_vars('question_row.answer_row', array(
						'ANSWER_NAME'		=> (strlen($answer_name) > 0) ? $answer_name : $user->lang['UQM_STATS_UNANSWERED'],
						'ANSWER_SELECTED'	=> number_format($percent, 1),
					));
				}
			}
		}
	}

	// Output the raw numbers for correct and incorrect for each question as well as the percentages
	function question_summary()
	{
		global $db, $template, $quiz_configuration;

		$sql_array = array(
			'SELECT'	=> 'q.question_name, s.quiz_question_id, q.question_bbcode_bitfield, 
						q.question_bbcode_uid, q.question_bbcode_options,
						COUNT(s.quiz_question_id) AS entries, SUM(s.quiz_is_correct) AS correct',

			'FROM'		=> array(QUIZ_STATISTICS_TABLE => 's', QUIZ_QUESTIONS_TABLE => 'q'),

			'WHERE'		=> $db->sql_in_set('s.quiz_question_id', $this->question_ids) . '
						AND s.quiz_question_id = q.question_id',

			'GROUP_BY'	=> 's.quiz_question_id, q.question_name, q.question_bbcode_bitfield, 
							q.question_bbcode_uid, q.question_bbcode_options',
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$question 	= generate_text_for_display($row['question_name'], $row['question_bbcode_uid'], $row['question_bbcode_bitfield'], $row['question_bbcode_options']);
			$plays		= $row['entries'];
			$correct	= $row['correct'];
			$incorrect	= $plays - $correct;

			$template->assign_block_vars('question_summary_row', array(
				'QUESTION'	=> $question,
				'CORRECT'	=> $correct,
				'INCORRECT'	=> $incorrect,
				'PERCENT'	=> $quiz_configuration->determine_percentage($correct, $incorrect),
			));
		}

		$db->sql_freeresult($result);
	}

	// See if a user has played a quiz, returns true or false
	function has_user_played_quiz($quiz_id, $user_id)
	{
		global $db;

		$sql = '
			SELECT COUNT(s.quiz_statistic_id) AS quiz_count
			FROM ' . QUIZ_STATISTICS_TABLE . ' s, ' . QUIZ_QUESTIONS_TABLE . ' q
			WHERE s.quiz_user = ' . (int) $user_id . '
				AND s.quiz_question_id = q.question_id
				AND q.question_quiz = ' . (int) $quiz_id;
		$result = $db->sql_query_limit($sql, 1);
		$stat_id = (int) $db->sql_fetchfield('quiz_count');
		$played = ($stat_id > 0);
		$db->sql_freeresult($result);

		return $played;
	}
}
