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
	'UQM_QUIZ'							=> 'Тест',
	'UQM_QUIZZES'						=> 'Тесты',
	'UQM_QUIZ_CONFIG_ERROR'				=> 'Обнаружена ошибка конфигурации.',
	'UQM_QUIZ_FOR_REGISTERED_USERS'		=> 'Только зарегистрированные пользователи могут проходить тесты.',

	'UQM_QUIZ_ADD'						=> 'Добавление теста',
	'UQM_STATS_QUIZ'					=> 'Статистика',
	'UQM_RECENTLY_ADDED_QUIZZES'		=> 'Последние добавленные тесты',
	'UQM_CATEGORY_NO_QUIZZES'			=> 'В этой категории нет тестов.',
	'UQM_CATEGORY_VIEW_ALL'				=> '%1$s Показать все тесты из этой категории %2$s', // Link
	'UQM_CATEGORY_NO_PERMISSION'		=> 'У вас нет доступа к этой категории тестов.',
	'UQM_CATEGORY_QUIZ_NO_PERMISSION'	=> 'У вас нет доступа к тестам из этой категории.',
	'UQM_CATEGORIES_NOT_AVAILABLE'		=> 'Нет категорий.',

	'UQM_SUBMIT_NO_PERMISSIONS'			=> 'Только администраторы могут добавлять новые тесты.',
	'UQM_ENTER_QUESTION'				=> 'Вопрос',
	'UQM_ENTER_QUESTION_EXPLAIN'		=> 'ББКоды разрешены в вопросах.',
	'UQM_ENTER_ANSWERS'					=> 'Ответы',
	'UQM_ENTER_ANSWERS_EXPLAIN'			=> 'Один ответ на строку, максимум ответов: %1$d. ББКоды не разрешены.',
	'UQM_PLUS_QUESTION'					=> 'Добавить вопрос',
	'UQM_MINUS_QUESTION'				=> 'Удалить последний вопрос',
	'UQM_QUESTION_BOUNDARY_VIOLATE'		=> 'Убедитесь, что количество вопросов находится в пределах от %1$d до %2$d.',
	'UQM_ENSURE_FIELDS_ARE_FILLED'		=> 'Убедитесь, что не оставили незаполненных полей.',
	'UQM_SELECT_ANSWERS'				=> 'Выберите ответ',
	'UQM_SELECT_ANSWERS_EXPLAIN'		=> 'Выберите один верный вариант ответа на каждый вопрос.',
	'UQM_ENTER_ALL_CORRECT'				=> 'Убедитесь, что заполнено название теста, выбрана категория и выбран верный ответ на каждый вопрос.',
	'UQM_ENTER_VALID_CATEGORY'			=> 'Выберите другую категорию. У вас нет доступа к выбранной категории.',
	'UQM_QUIZ_ADDED'					=> 'Тест успешно создан. <br /> %1$s Вернуться на главную %2$s', // Link
	'UQM_ENTER_QUIZ_NAME'				=> 'Введите название теста',
	'UQM_ENTER_QUIZ_CATEGORY'			=> 'Выберите категорию',

	'UQM_QUIZ_NAME'						=> 'Название',
	'UQM_QUIZ_ADDED_BY'					=> 'Автор: %1$s', // Username
	'UQM_QUIZZES_NO_ENTRIES'			=> 'В этой категории пока нет ни одного теста. Нажмите здесь, чтобы создать первый тест',

	'UQM_QUIZ_PLAY_NO_ID'				=> 'Тест не выбран.',
	'UQM_QUIZ_AUTHOR_DETAILS'			=> 'Автор: %1$s » %2$s', // Username & datetime
	'UQM_QUIZ_CORRECT'					=> 'Верно',
	'UQM_QUIZ_INCORRECT'				=> 'Неверно',
	'UQM_QUIZ_USER_SELECTED'			=> 'вы выбрали <b>%1$s</b>, верный ответ <b>%2$s</b>.',
	'UQM_QUIZ_CASH_GAIN'				=> 'Вы получили <b>%1$d</b> %2$s за высокий процент верных ответов в этом тесте.', // N points
	'UQM_QUIZ_CASH_LOST'				=> 'Вы потеряли <b>%1$d</b> %2$s из-за низкого процента верных ответов в этом тесте.', // N points
	'UQM_RESULTS_FOR_QUIZ'				=> 'Результат теста «%1$s»: %2$d%%', // Quiz title & percent
	'UQM_RESULTS_SUMMARY'				=> 'Вы дали верных ответов: <b>%1$d</b>, неверных: <b>%2$d</b>. Ваш результат: <b>%3$d%%</b>.',
	'UQM_RESULTS_GROUP_REWARD'			=> 'Вы прошли все тесты в этой категории с процентом верных ответов не ниже <b>%1$d%%</b> и поэтому были включены в группу <b>%2$s</b>.',
	'UQM_RETURN_TO_INDEX'				=> 'Вернуться на главную',

	'UQM_QUIZ_STATS'					=> 'Статистика теста',

	'UQM_STATS_QUESTION'				=> 'Вопрос',
	'UQM_STATS_QUESTIONS'				=> 'Вопросов',
	'UQM_STATS_CORRECT_ANSWERS'			=> 'Верных ответов',
	'UQM_STATS_INCORRECT_ANSWERS'		=> 'Неверных ответов',
	'UQM_STATS_CORRECT_PERCENT'			=> 'Процент верных',
	'UQM_STATS_INCORRECT_PERCENT'		=> 'Процент неверных',
	'UQM_STATS_TIMES_PLAYED'			=> 'Прохождений',
	'UQM_STATS_AVERAGE_SCORE'			=> 'Средний результат',
	'UQM_STATS_CANNOT_VIEW'				=> 'Статистику теста могут просматривать только те, кто прошёл этот тест, а также его автор и администраторы.',
	'UQM_STATS_NO_ENTRIES'				=> 'Нет данных',
	'UQM_STATS_ANSWER'					=> 'Ответы пользователей',
	'UQM_STATS_UNANSWERED'				=> 'Без ответа',

	'UQM_EDIT_NOT_ALLOWED'				=> 'У вас нет права редактировать этот тест.',
	'UQM_QUIZ_EDIT'						=> 'Редактирование теста',
	'UQM_DELETE_QUIZ'					=> 'Удалить тест',
	'UQM_DELETE_QUIZ_EXPLAIN'			=> 'Поставьте этот флажок, если хотите удалить тест.',
	'UQM_EDIT_VERIFY_ANSWERS'			=> 'Ошибка. Убедитесь, что вы выбрали верный ответ на каждый вопрос и что каждый вопрос заполнен корректно.',
	'UQM_QUIZ_EDIT_SUBMITTED'			=> 'Тест успешно отредактирован. <br /> %1$s Вернуться на главную %2$s',
	'UQM_DELETE_QUIZ_SUBMITTED'			=> 'Тест удалён. <br /> %1$s Вернуться на главную %2$s',
	'UQM_EDIT_NO_QUIZ'					=> 'Тест не найден.',

	'UQM_INDEX_STATS'					=> '%1$s Статистика %2$s', // Link
	'UQM_INDEX_EDIT'					=> '%1$s Редактировать %2$s', // Link

	'UQM_TIME_LIMIT_VIOLATED'			=> 'Вам придётся подождать <b>%1$d %2$s</b>, прежде чем начать этот тест заново, поскольку предыдущая ваша попытка не была завершена.', // N minutes
	'UQM_TIME_LIMIT_EXCEEDED'			=> 'Время, отведённое на прохождение теста, истекло.',
	'UQM_TIME_LIMIT_EXCEEDED_REDIRECT'	=> 'Время, отведённое на прохождение теста, истекло. Нажмите OK, чтобы вернуться на главную.',
	'UQM_END_SESSION_ERROR'				=> 'Невозможно завершить сессию, поскольку сессия для этого теста не найдена.',
	'UQM_ENTER_TIME_LIMIT'				=> 'Лимит времени на прохождение теста (0 = без лимита)',
	'UQM_TIME_LIMIT_MINUTES'			=> 'минут',
	'UQM_TIME_LIMIT_SECONDS'			=> 'секунд',
	'UQM_BUTTON_QUIZ_NEW'				=> 'Добавить тест',

	'UQM_NUMBER_SIGN'					=> '№',
	'UQM_NONE_SELECTED'					=> 'ничего',
	'UQM_GO_BACK'						=> 'Вернуться назад',
	'UQM_NEXT_STEP'						=> 'Продолжить',
	'UQM_CASH_ERROR'					=> '[Ultimate Quiz] Невозможно изменить баланс баллов пользователя из-за ошибки: <br /> %1$s',

	'UQM_MINUTES'						=> [
		1	=> 'минуту',
		2	=> 'минуты',
		3	=> 'минут',
	],
	'UQM_POINTS'						=> [
		1	=> 'балл',
		2	=> 'балла',
		3	=> 'баллов',
	],
]);
