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
	'ACP_UQM_QUIZ_TITLE'		=> 'Ultimate Quiz',
	'ACP_UQM_QUIZ_SETTINGS'		=> 'Настройки',
]);
