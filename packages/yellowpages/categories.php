<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

$currentCategory = isset($_GET['c']) && is_numeric($_GET['c']) ? (int)$_GET['c'] : null;
$parentId = isset($_GET['p']) ? (int)$_GET['p'] : -1;
$categoryId = isset($_GET['i']) ? (int)$_GET['i'] : null;

if ($categoryId === null)
{
	$categories[] = array('id' => 1, 'title' => iaLanguage::get('all'));
}
else
{
	$categories = $iaCore->iaDb->all('`id`, `title`, `child`', '`parent_id` = ' . $categoryId . ' ORDER BY `title`', null, null, 'yp_categories');
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