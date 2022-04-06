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
	'ACP_UQM_QUIZ_SETTINGS'			=> 'Ultimate Quiz — настройки',
	'ACP_UQM_QUIZ_CATEGORIES'		=> 'Категории',
	'ACP_UQM_QUIZ_CONFIGURATION'	=> 'Конфигурация',
	'ACP_UQM_CATEGORY_EDIT'			=> 'Ultimate Quiz — редактирование категории',
	'ACP_UQM_CATEGORY_ADD'			=> 'Ultimate Quiz — создание категории',

	'ACP_UQM_EDIT_CATEGORY'			=> 'Редактировать категорию',
	'ACP_UQM_DELETE_CATEGORY'		=> 'Удалить категорию',
	'ACP_UQM_ADD_CATEGORY'			=> '%s Добавить новую категорию %s',

	'ACP_UQM_CATEGORY_INFO'									=> 'Название и описание',
	'ACP_UQM_CATEGORY_NAME'									=> 'Название категории',
	'ACP_UQM_CATEGORY_NAME_EXPLAIN'							=> 'Обязательно.',
	'ACP_UQM_CATEGORY_DESCRIPTION'							=> 'Описание категории',
	'ACP_UQM_CATEGORY_DESCRIPTION_EXPLAIN'					=> 'Можно оставить пустым.',
	'ACP_UQM_CATEGORY_NAME_VALIDATE'						=> 'Название категории не должно быть пустым или совпадать с уже существующим.',
	'ACP_UQM_CATEGORY_GROUP_REWARDS'						=> 'Автовключение в группу',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_EXPLAIN'				=> 'Если пользователь решит все тесты в этой категории с определённым процентом верных ответов, он будет автоматически включён в целевую группу.',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP'					=> 'Целевая группа',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP_SELECT'			=> 'Выберите группу...',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP_EXPLAIN'			=> 'Здесь отображаются только созданные администратором группы. Автовключение в предустановленные группы («Супермодераторы» и т.п.) невозможно.',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_GROUP_VALIDATE'			=> 'Целевая группа должна быть указана и не должна быть одной из предустановленных групп («Супермодераторы» и т.п.).',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_PERCENTAGE'				=> 'Минимальный процент верных ответов',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_PERCENTAGE_EXPLAIN'		=> 'Пользователь должен достичь указанного процента в каждом тесте этой категории.',
	'ACP_UQM_CATEGORY_GROUP_REWARDS_PERCENTAGE_VALIDATE'	=> 'Минимальный процент верных ответов должен быть целым числом от 0 до 100.',
	'ACP_UQM_CATEGORY_ADDED'								=> 'Категория <strong>%s</strong> успешно создана.',
	'ACP_UQM_CATEGORY_UPDATED'								=> 'Категория <strong>%s</strong> успешно отредактирована.',
	'ACP_UQM_CATEGORY_GROUP_PERMISSIONS'					=> 'Доступ',
	'ACP_UQM_CATEGORY_GROUP_PERMISSIONS_GROUP'				=> 'Группы, имеющие доступ к этой категории',
	'ACP_UQM_CATEGORY_GROUP_PERMISSIONS_GROUP_EXPLAIN'		=> 'Удерживайте клавишу Ctrl, если хотите выбрать несколько групп.',

	'ACP_UQM_DELETE_TITLE'			=> 'Ultimate Quiz — удаление категории',
	'ACP_UQM_DELETE_TITLE_CONFIRM'	=> 'Точно хотите удалить эту категорию и все тесты в ней?',

	'ACP_UQM_DELETE_SUCCESSFUL'		=> 'Категория удалена.',

	'ACP_UQM_CONFIG_UPDATED'		=> 'Конфигурация успешно обновлена.',

	'ACP_UQM_CONFIG_DEFINITIONS'	=> array(
		'qc_minimum_questions'			=> 'Минимум вопросов',
		'qc_minimum_questions_explain'	=> 'Минимально допустимое количество вопросов в тесте.',
		'qc_maximum_questions'			=> 'Максимум вопросов',
		'qc_maximum_questions_explain'	=> 'Максимально допустимое количество вопросов в тесте.',
		'qc_maximum_answers'			=> 'Максимум вариантов ответа',
		'qc_maximum_answers_explain'	=> 'Максимально допустимое количество вариантов ответа на каждый вопрос.',
		'qc_show_answers'				=> 'Показать ответы',
		'qc_show_answers_explain'		=> 'Детально показать пользователю правильные ответы после прохождения теста. Если «Нет», будет показан только процент верных ответов.',
		'qc_quiz_author_edit'			=> 'Разрешить авторам редактировать свои тесты',
		'qc_quiz_author_edit_explain'	=> 'Обычный пользователь (не администратор) сможет редактировать и удалять свои тесты. Если «Нет», это разрешено только администратору.',
		'qc_admin_submit_only'			=> 'Разрешить создание тестов только администратору',
		'qc_admin_submit_only_explain'	=> 'Обычный пользователь (не администратор) не сможет создавать новые тесты.',
		'qc_enable_time_limits'			=> 'Разрешить таймеры',
		'qc_enable_time_limits_explain'	=> 'Автор теста сможет по своему желанию установить лимит времени, до истечения которого пользователь должен успеть ответить на все вопросы.',
		'qc_exclusion_time'				=> 'Таймаут против читеров',
		'qc_exclusion_time_explain'		=> 'Таймаут (в секундах) до следующей попытки прохождения теста, если предыдущая была неудачна или не завершена. Введите 0, если таймаут не нужен.',
		'qc_quizzes_per_page'			=> 'Тестов на страницу',
		'qc_quizzes_per_page_explain'	=> 'Количество тестов на одну страницу в каждой категории.',
		'qc_quizzes_on_index'			=> 'Тестов на категорию на главной странице',
		'qc_quizzes_on_index_explain'	=> 'Количество тестов из каждой категории для отображеня на главной странице.',
		'qc_cash_enabled'				=> 'Включить интеграцию с системой баллов/пойнтов/денег',
		'qc_cash_enabled_explain'		=> 'Автоматически добавлять/убавлять пользователю баллы/пойнты/деньги за верные/неверные ответы, если установлено какое-либо расширение типа Points System. Не включайте эту функцию, если не до конца понимаете, что делаете.',
		'qc_cash_column'				=> 'Колонка баллов в базе данных',
		'qc_cash_column_explain'		=> 'Название колонки в таблице пользователей, где хранится баланс баллов.',
		'qc_cash_correct'				=> 'Плюс баллов за верный ответ',
		'qc_cash_correct_explain'		=> 'Сколько баллов будет добавлено пользователю за каждый верный ответ.',
		'qc_cash_incorrect'				=> 'Минус баллов за неверный ответ',
		'qc_cash_incorrect_explain'		=> 'Сколько баллов будет отнято у пользователя за каждый неверный ответ.',
	),
));
