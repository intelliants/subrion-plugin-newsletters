<?php
//##copyright##

$currentCategory = isset($_GET['c']) && is_numeric($_GET['c']) ? (int)$_GET['c'] : null;
$parentId = isset($_GET['p']) ? (int)$_GET['p'] : -1;
$parentId = $parentId == 0 ? 1 : $parentId;

$categoryId = isset($_GET['i']) ? (int)$_GET['i'] : null;

if ($categoryId === null)
{
	$categories[] = array('id' => 1, 'title' => iaLanguage::get('all'));
}
else
{
	$categories = $iaCore->iaDb->all('`id`, `title`, `child`', '`parent_id` = ' . $categoryId . ' ORDER BY `title`', null, null, 'coupons_categories');
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
		'state' => ($item['child'] == $item['id'])  ? 'leaf' : 'closed'
	);
}
$iaView->assign($out);