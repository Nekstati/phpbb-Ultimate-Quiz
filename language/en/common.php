<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'UQM_QUIZ'							=> 'Quiz',
	'UQM_QUIZZES'						=> 'Quizzes',
	'UQM_QUIZ_CONFIG_ERROR'				=> 'A configuration error has been detected.',
	'UQM_QUIZ_FOR_REGISTERED_USERS'		=> 'Only registered users may view quizzes.',

	'UQM_QUIZ_ADD'						=> 'Add new quiz',
	'UQM_STATS_QUIZ'					=> 'Statistics',
	'UQM_RECENTLY_ADDED_QUIZZES'		=> 'Recently added quizzes',
	'UQM_CATEGORY_NO_QUIZZES'			=> 'There are no quizzes in this category.',
	'UQM_CATEGORY_VIEW_ALL'				=> '%1$s View all quizzes from this category %2$s', // Link
	'UQM_CATEGORY_NO_PERMISSION'		=> 'You do not have the required permissions to view this category.',
	'UQM_CATEGORY_QUIZ_NO_PERMISSION'	=> 'You do not have the required permissions to view quizzes from this category.',
	'UQM_CATEGORIES_NOT_AVAILABLE'		=> 'There are no quiz categories to display.',

	'UQM_SUBMIT_NO_PERMISSIONS'			=> 'Only administrators are permitted to submit quizzes.',
	'UQM_ENTER_QUESTION'				=> 'Question',
	'UQM_ENTER_QUESTION_EXPLAIN'		=> 'BBCode is permitted in questions.',
	'UQM_ENTER_ANSWERS'					=> 'Answers',
	'UQM_ENTER_ANSWERS_EXPLAIN'			=> 'One answer per line, maximum answers: %1$d. BBCode is not permitted.',
	'UQM_PLUS_QUESTION'					=> 'Add question',
	'UQM_MINUS_QUESTION'				=> 'Remove last question',
	'UQM_QUESTION_BOUNDARY_VIOLATE'		=> 'Please ensure the number of questions stays between %1$d and %2$d as set by the administrator.',
	'UQM_ENSURE_FIELDS_ARE_FILLED'		=> 'Please ensure no fields are left empty.',
	'UQM_SELECT_ANSWERS'				=> 'Select the correct answer',
	'UQM_SELECT_ANSWERS_EXPLAIN'		=> 'In the answers below, please select one correct answer for each question.',
	'UQM_ENTER_ALL_CORRECT'				=> 'Please ensure a quiz name, quiz category and a correct answer for each question has been specified.',
	'UQM_ENTER_VALID_CATEGORY'			=> 'Please select another category, as you do not have access to the category currently selected.',
	'UQM_QUIZ_ADDED'					=> 'The quiz has now been submitted into the database. <br /> %1$s Return to the quiz index page %2$s', // Link
	'UQM_ENTER_QUIZ_NAME'				=> 'Enter the quiz name',
	'UQM_ENTER_QUIZ_CATEGORY'			=> 'Select a quiz category',

	'UQM_QUIZ_NAME'						=> 'Quiz name',
	'UQM_QUIZ_ADDED_BY'					=> 'Created by %1$s', // Username
	'UQM_QUIZZES_NO_ENTRIES'			=> 'No quizzes have been submitted yet. Click here to submit the first right now',

	'UQM_QUIZ_PLAY_NO_ID'				=> 'No quiz has been selected. Please select a quiz.',
	'UQM_QUIZ_AUTHOR_DETAILS'			=> 'Created by %1$s on %2$s', // Username & datetime
	'UQM_QUIZ_CORRECT'					=> 'Correct',
	'UQM_QUIZ_INCORRECT'				=> 'Incorrect',
	'UQM_QUIZ_USER_SELECTED'			=> 'you selected <b>%1$s</b>, the correct answer was <b>%2$s</b>.',
	'UQM_QUIZ_CASH_GAIN'				=> 'You have gained <b>%1$d</b> %2$s from playing this quiz.', // N points
	'UQM_QUIZ_CASH_LOST'				=> 'You have lost <b>%1$d</b> %2$s from playing this quiz.', // N points
	'UQM_RESULTS_FOR_QUIZ'				=> 'Result for “%1$s”: %2$d%%', // Quiz title & percent
	'UQM_RESULTS_SUMMARY'				=> 'You correctly answered <b>%1$d</b> and incorrectly answered <b>%2$d</b> questions, your result is <b>%3$d%%</b>.',
	'UQM_RESULTS_GROUP_REWARD'			=> 'For achieving at least <b>%1$d%%</b> in each of the quizzes in this category, you have been added to the <b>%2$s</b> usergroup.',
	'UQM_RETURN_TO_INDEX'				=> 'Return to the quiz index',

	'UQM_QUIZ_STATS'					=> 'Quiz statistics',

	'UQM_STATS_QUESTION'				=> 'Question',
	'UQM_STATS_QUESTIONS'				=> 'Questions',
	'UQM_STATS_CORRECT_ANSWERS'			=> 'Times answered correctly',
	'UQM_STATS_INCORRECT_ANSWERS'		=> 'Times answered incorrectly',
	'UQM_STATS_CORRECT_PERCENT'			=> 'Correct percentage',
	'UQM_STATS_INCORRECT_PERCENT'		=> 'Incorrect percentage',
	'UQM_STATS_TIMES_PLAYED'			=> 'Times played',
	'UQM_STATS_AVERAGE_SCORE'			=> 'Average score',
	'UQM_STATS_CANNOT_VIEW'				=> 'Only administrators, the quiz author and users who have played the quiz may view the quiz statistics.',
	'UQM_STATS_NO_ENTRIES'				=> 'There are no entries',
	'UQM_STATS_ANSWER'					=> 'User answers',
	'UQM_STATS_UNANSWERED'				=> 'Unanswered',

	'UQM_EDIT_NOT_ALLOWED'				=> 'You do not have the required permissions to edit this quiz.',
	'UQM_QUIZ_EDIT'						=> 'Edit quiz',
	'UQM_DELETE_QUIZ'					=> 'Delete quiz',
	'UQM_DELETE_QUIZ_EXPLAIN'			=> 'Tick the following box only if wish to delete this quiz.',
	'UQM_EDIT_VERIFY_ANSWERS'			=> 'An error occured, please ensure you have selected a valid answer(s) for each question and that each question is valid.',
	'UQM_QUIZ_EDIT_SUBMITTED'			=> 'The changes to the quiz have now been submitted into the database. <br /> %1$s Return to the quiz index page %2$s',
	'UQM_DELETE_QUIZ_SUBMITTED'			=> 'The quiz has now been completely removed from the database. <br /> %1$s Return to the quiz index page %2$s',
	'UQM_EDIT_NO_QUIZ'					=> 'This quiz could not be found.',

	'UQM_INDEX_STATS'					=> '%1$s Statistics %2$s', // Link
	'UQM_INDEX_EDIT'					=> '%1$s Edit or delete %2$s', // Link

	'UQM_TIME_LIMIT_VIOLATED'			=> 'You cannot play this quiz for another <b>%1$d %2$s</b>, since you did not finish the quiz in the previous attempt.', // N minutes
	'UQM_TIME_LIMIT_EXCEEDED'			=> 'You have exceeded the time limit allowed for this quiz.',
	'UQM_TIME_LIMIT_EXCEEDED_REDIRECT'	=> 'You have exceeded the time limit allowed for this quiz. Click OK to be redirected back to the quiz index page.',
	'UQM_END_SESSION_ERROR'				=> 'Unable to end the session, as no session for this quiz could be found.',
	'UQM_ENTER_TIME_LIMIT'				=> 'Enter the time limit for this quiz, or set to 0 for no time limit',
	'UQM_TIME_LIMIT_MINUTES'			=> 'minutes',
	'UQM_TIME_LIMIT_SECONDS'			=> 'seconds',
	'UQM_BUTTON_QUIZ_NEW'				=> 'Create a new quiz',

	'UQM_NUMBER_SIGN'					=> '#',
	'UQM_NONE_SELECTED'					=> 'none',
	'UQM_GO_BACK'						=> 'Go back',
	'UQM_NEXT_STEP'						=> 'Next step',
	'UQM_CASH_ERROR'					=> '[Ultimate Quiz] Unable to change user’s cash/points balance due to an error: <br /> %1$s',

	'UQM_MINUTES'						=> [
		1	=> 'minute',
		2	=> 'minutes',
	],
	'UQM_POINTS'						=> [
		1	=> 'point',
		2	=> 'points',
	],
]);
