<?php

namespace nekstati\ultimatequiz\core;

class quiz_question
{
	var $question_id;
	var $question;
	var $answers;
	var $correct;

	function initialise($question, $answers, $correct, $question_id = -1)
	{
		global $quiz_configuration;

		$this->answers	= array();
		$this->question	= $question;
		$this->correct	= $correct;

		// Only allow up to the defined number of answers, and exclude any blank answers
		for ($i = 0; $i < $quiz_configuration->value('qc_maximum_answers'); $i++)
		{
			if (!empty($answers[$i]))
			{
				$this->answers[$i] = $answers[$i];
			}
		}

		$this->question_id = ($question_id >= 0) ? $question_id : null;
	}

	function insert($question_array, $quiz_name, $quiz_category, $time_limit = null)
	{
		global $db, $user;

		$quiz_array = array(
			'quiz_name'			=> utf8_normalize_nfc($quiz_name),
			'quiz_time'			=> time(),
			'quiz_category'		=> (int) $quiz_category,
			'user_id'			=> (int) $user->data['user_id'],
			'username'			=> $user->data['username_clean'],
			'user_colour'		=> $user->data['user_colour'],
			'quiz_time_limit'	=> (int) $time_limit,
		);

		$db->sql_query('INSERT INTO ' . QUIZ_TABLE . ' ' . $db->sql_build_array('INSERT', $quiz_array));

		$quiz_id = $db->sql_nextid();

		$question_insert = array();

		foreach ($question_array as $question_data)
		{
			$data_answers = $question_data->show_answers();
			$correct_value = $data_answers[$question_data->correct];

			if (in_array($correct_value, $data_answers))
			{
				// Prepare the question name to allow bbCode
				$uid = $bitfield = $options = '';
				$allow_bbcode = $allow_urls = $allow_smilies = true;

				$question_name = $question_data->question;
				generate_text_for_storage($question_name, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

				$question_insert[] = array(
					'question_name'				=> utf8_normalize_nfc($question_name),
					'question_correct'			=> utf8_normalize_nfc($correct_value),
					'question_answers'			=> utf8_normalize_nfc($question_data->show_answers(true)),
					'question_quiz'				=> (int) $quiz_id,

					'question_bbcode_bitfield'	=> $bitfield,
					'question_bbcode_uid'		=> $uid,
					'question_bbcode_options'	=> $options,
				);
			}
		}

		$db->sql_multi_insert(QUIZ_QUESTIONS_TABLE, $question_insert);
	}

	// To update an array of questions in the database, ie. such as when editing a quiz
	function update($question_array, $quiz_name, $quiz_id, $quiz_category, $time_limit = null)
	{
		global $db;

		// Update the quiz name, quiz category and time limit
		$new_name = '';
		$new_time_limit = '';

		// Quiz name
		if (!empty($quiz_name))
		{
			$new_name = "quiz_name = '";
			$new_name .= $db->sql_escape(utf8_normalize_nfc($quiz_name));
			$new_name .= "', ";
		}

		// Quiz time limit
		if (!empty($time_limit))
		{
			$new_time_limit = 'quiz_time_limit = ' . (int) $time_limit . ', ';
		}

		$db->sql_query('
			UPDATE ' . QUIZ_TABLE . '
			SET ' . $new_name . $new_time_limit . ' quiz_category = ' . (int) $quiz_category . '
			WHERE quiz_id = ' . (int) $quiz_id);

		foreach ($question_array as $question)
		{
			// Prepare the question name to allow bbCode
			$uid = $bitfield = $options = '';
			$allow_bbcode = $allow_urls = $allow_smilies = true;

			$question_name = $question->show_question();
			generate_text_for_storage($question_name, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

			// Actually update the question data now
			$sql_data = array(
				'question_name'				=> utf8_normalize_nfc($question_name),
				'question_correct'			=> utf8_normalize_nfc($question->show_correct()),
				'question_answers'			=> utf8_normalize_nfc($question->show_answers(true)),
				'question_quiz'				=> (int) $quiz_id,
				'question_bbcode_bitfield'	=> $bitfield,
				'question_bbcode_uid'		=> $uid,
				'question_bbcode_options'	=> $options,
			);

			$sql = '
				UPDATE ' . QUIZ_QUESTIONS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_data) . '
				WHERE question_id = ' . (int) $question->show_question_id();
			$db->sql_query($sql);
		}
	}

	// Preparation for editing a quiz
	function edit($quiz_id)
	{
		return $this->get_question_data($quiz_id, 'edit');
	}

	// Delete a quiz and all of its contents
	function delete($quiz_id, $question_ids)
	{
		global $db;

		// So we want to delete from the quiz, quiz questions and quiz statistics tables
		$sql = array();
		$sql[] = 'DELETE FROM ' . QUIZ_STATISTICS_TABLE . ' WHERE ' . $db->sql_in_set('quiz_question_id', $question_ids);
		$sql[] = 'DELETE FROM ' . QUIZ_TABLE . ' WHERE quiz_id = ' . (int) $quiz_id;
		$sql[] = 'DELETE FROM ' . QUIZ_QUESTIONS_TABLE . ' WHERE question_quiz = ' . (int) $quiz_id;

		foreach ($sql as $query)
		{
			// Perform the query
			$db->sql_query($query);
		}
	}

	// Preparation for playing a quiz
	function play($quiz_id)
	{
		return $this->get_question_data($quiz_id, 'play');
	}

	// Get question data
	function get_question_data($quiz_id, $mode = 'play')
	{
		global $db;

		// The purpose of this function is to create an array of objects of each question for the quiz
		$sql = '
			SELECT * FROM ' . QUIZ_QUESTIONS_TABLE . '
			WHERE question_quiz = ' . $quiz_id;
		$result = $db->sql_query($sql);
		$object_array = array();

		while ($row = $db->sql_fetchrow($result))
		{
			if ($mode == 'play')
				$row['question_name'] = generate_text_for_display($row['question_name'], $row['question_bbcode_uid'], $row['question_bbcode_bitfield'], $row['question_bbcode_options']);
			else // Edit mode
				decode_message($row['question_name'], $row['question_bbcode_uid']);

			$quiz_question = new quiz_question;
			$quiz_question->initialise($row['question_name'], explode("\n", $row['question_answers']), $row['question_correct'], $row['question_id']);

			$object_array[] = $quiz_question;
		}

		$db->sql_freeresult($result);

		return $object_array;
	}


	// Get the incomplete and current sessions for this user and for this quiz
	function get_sessions($quiz_id, $user_id, $current_time, $starting_new_session = false)
	{
		global $db, $quiz_configuration;

		// We want to see if there are any existing quiz sessions for this user and for this quiz, where
		// the started time + the exclusion period is greater than the current time. So the user is still excluded.
		$sql = '
			SELECT s.*, q.quiz_time_limit
			FROM ' . QUIZ_SESSIONS_TABLE . ' s, ' . QUIZ_TABLE . ' q
			WHERE s.quiz_id = q.quiz_id
				AND s.user_id = ' . (int) $user_id . '
				AND s.quiz_id = ' . (int) $quiz_id . '
				AND s.started > 0
				AND s.ended = 0';
		if ($starting_new_session && $quiz_configuration->value('qc_exclusion_time') > 0)
			$sql .= '
				AND s.started > ' . ($current_time - (int) $quiz_configuration->value('qc_exclusion_time'));
		$sql .= '
			ORDER BY s.started DESC';

		// Note: the exclusion period can be independent of the time limits. For example, even though a user could open
		// a quiz and then close it even if there is no time limit for that quiz they should still be punished
		// with the exclusion period because they could be doing it just to inflate their statistics records.
		$result = $db->sql_query($sql);
		$object_array = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		return $object_array;
	}

	// Inserts a new quiz session if the user has not recently played this quiz and failed to finish
	// (validation against cheating - if a user doesn't finish a quiz they must wait X amount of time before re-playing)
	function insert_quiz_session($quiz_id)
	{
		global $user, $db, $quiz_configuration;

		// Only worry about this if time limits are enabled
		if ($quiz_configuration->value('qc_exclusion_time') > 0)
		{
			// Exclusion time
			$current_time = time();

			$sessions = $this->get_sessions($quiz_id, $user->data['user_id'], $current_time, true);

			// Get the most recent starting time (for sessions by this user for this quiz that have not ended)
			$started_time = (sizeof($sessions)) ? $sessions[0]['started'] : -1;

			// If there are results, then the user has recently started a quiz without finishing
			// ie. they violated the time limit restriction
			if ($started_time > 0)
			{
				// All of this is ignored if the user is not currently in a valid session
				$exclusion_period_expire_time = $started_time + (int) $quiz_configuration->value('qc_exclusion_time');
				$exclusion_period_time_remaining = $exclusion_period_expire_time - $current_time;

				// If true, the user still has time to serve on their exclusion
				if ($exclusion_period_time_remaining > 0)
				{
					// Don't display seconds to the user. Round up to the nearest minute.
					$minutes_remaining = ceil($exclusion_period_time_remaining / 60);
					trigger_error($user->lang('UQM_TIME_LIMIT_VIOLATED', $minutes_remaining, $user->lang('UQM_MINUTES', (int) $minutes_remaining)));
				}
			}
		}

		// If validations have passed then the user is okay to play the quiz, so we add a quiz session row.
		$quiz_session_array = array(
			'quiz_id'	=> (int) $quiz_id,
			'user_id'	=> (int) $user->data['user_id'],
			'started'	=> time(),
			'ended'		=> 0,
		);

		$db->sql_query('INSERT INTO ' . QUIZ_SESSIONS_TABLE . ' ' . $db->sql_build_array('INSERT', $quiz_session_array));
	}

	// Ends a quiz session
	function update_quiz_session($quiz_id)
	{
		global $user, $db, $quiz_configuration;

		$current_time = time();

		$sessions = $this->get_sessions($quiz_id, $user->data['user_id'], $current_time);

		// Use the latest session
		$quiz_session_id = (sizeof($sessions)) ? $sessions[0]['quiz_session_id'] : -1;

		// Check that the time limit hasn't expired
		if (sizeof($sessions))
		{
			$recent_session = $sessions[0];

			$started_time = $recent_session['started'];
			$quiz_time_limit = $recent_session['quiz_time_limit'];

			// This is the final time the user can submit
			$last_finish_time_allowed = $started_time + $quiz_time_limit;

			// If that time is exceeded (and this quiz doesn't have a time limit of 0), show the error.
			if ($quiz_time_limit > 0 && $current_time > $last_finish_time_allowed && $quiz_configuration->value('qc_enable_time_limits'))
			{
				// It has expired
				trigger_error('UQM_TIME_LIMIT_EXCEEDED');
			}
		}

		// End the quiz session
		if ($quiz_session_id > 0)
		{
			// Set the ending time for the quiz session
			$sql = '
				UPDATE ' . QUIZ_SESSIONS_TABLE . '
				SET ended = ' . $current_time . '
				WHERE quiz_session_id = ' . $quiz_session_id;
			$db->sql_query($sql);
		}
		// This generally shouldn't happen. It is a safeguard against a user trying to submit a quiz twice though.
		else
		{
			trigger_error('UQM_END_SESSION_ERROR');
		}

		return $quiz_session_id;
	}

	// Output the results, $actual is the actual answer while $submitted is what the user chose
	function obtain_result_data($actual = null, $submitted = null, $question_id = null, $quiz_session_id = null)
	{
		global $user, $db;
		static $statistics_array = array();

		// Run the database query
		if( empty($actual) && empty($submitted) )
		{
			// Enforce the session id on the statistics
			foreach ($statistics_array as &$statistic)
			{
				// Update the reference
				$statistic['quiz_session_id'] = (int) $quiz_session_id;
			}

			$db->sql_multi_insert(QUIZ_STATISTICS_TABLE, $statistics_array);
			$question_result = null;
		}
		// Continue to build the results
		else
		{
			// We will use the actual string results rather than array position for posterity - as any
			// edits may change those array positions.

			$statistics_array[] = array(
				'quiz_question_id'		=> (int) $question_id,
				'quiz_user_answer'		=> (string) $submitted,
				'quiz_actual_answer'	=> (string) $actual,
				'quiz_is_correct'		=> (int) ($submitted == $actual) ? 1 : 0,
				'quiz_user'				=> (int) $user->data['user_id'],
				'quiz_session_id'		=> null,
			);

			$question_result = sprintf($user->lang['UQM_QUIZ_USER_SELECTED'], ($submitted ?: $user->lang['UQM_NONE_SELECTED']), $actual);
		}

		return $question_result;
	}

	// Obtain question data from the submit page upon adding or removing, etc
	function refresh_obtain(&$any_empty = false)
	{
		global $quiz_configuration;

		$object_array = array();
		$check_empty = false;

		// Firstly, loop through the questions
		for ($i = 0; $i < request_var('question_number', 0); $i++)
		{
			$question_name	= request_var("question_name_$i", '', true);
			$answers_name	= request_var("answers_$i", '', true);
			$multiples		= explode("\n", $answers_name);
			$correct		= request_var("answer_$i", '', true);

			if (!$check_empty)
			{
				$check_empty = ((strlen($question_name) < 1) || (strlen($answers_name) < 1));
			}

			$object = new quiz_question;
			$object->initialise($question_name, $multiples, $correct);

			$object_array[] = $object; // Add the question object to the list
		}

		$any_empty = $check_empty;

		return $object_array;
	}

	// Accessor for question id
	function show_question_id()
	{
		return $this->question_id;
	}

	// Accessor for question
	function show_question()
	{
		return $this->question;
	}

	// Accessor for the correct answer
	function show_correct()
	{
		return $this->correct;
	}

	// Accessor for answers; if condensed, show it in a linear form. If false, return the actual array
	function show_answers($condensed = false)
	{
		return ($condensed) ? implode("\n", $this->answers) : $this->answers;
	}

	// This function MUST only be uncommented during testing or debugging as the phpBB language mechanism is not used
	function DEBUG()
	{
		echo 'Question ID: ' . $this->question_id;
		echo '<br />';
		echo 'Question: ' . $this->question;
		echo '<br />';
		echo 'Correct answer: ' . $this->correct;
		echo '<br />';
		echo 'Answers: ';
		print_r($this->answers);
		echo '<br /><br />';
	}
}
