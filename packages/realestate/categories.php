<?php
//##copyright##

$currentCategory = isset($_GET['c']) && is_numeric($_GET['c']) ? (int)$_GET['c'] : null;
$parentId = isset($_GET['p']) ? (int)$_GET['p'] : -1;
$categoryId = isset($_GET['i']) ? (int)$_GET['i'] : null;

if ($categoryId === null)
{
	$categories[] = array('id' => 0, 'title' => iaLanguage::get('all'));
}
else
{
	$sql =	"SELECT `id`, `title` ";
	$sql .= "FROM `" . $iaCore->iaDb->prefix . "locations` ";
	$sql .= "LEFT JOIN `" . $iaCore->iaDb->prefix . "locations_tree` ON `" . $iaCore->iaDb->prefix . "locations`.`id` = `" . $iaCore->iaDb->prefix . 'locations_tree`.`nid` ';
	$sql .= "WHERE `parent` = " . $categoryId;

	$categories = $iaCore->iaDb->getAll($sql, null, null);
}

$out = array();

foreach ($categories as $item)
{
	$rel = 'default';

	$out[] = array(
		'attr' => array(
			'id' => $item['id'],
			'rel' => $rel,
			'title' => $item['id'] == $currentCategory ? iaLanguage::get('locked') : '',
		),
		'cls' => 'folder',
		'data' => $item['title'],
		'state' => 'closed'
	);
}
$iaView->assign($out);