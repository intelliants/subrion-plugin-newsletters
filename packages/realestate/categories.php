<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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

if (null === $categoryId) {
    $categories[] = ['id' => 0, 'title' => iaLanguage::get('all')];
} else {
    $sql = "SELECT `id`, `title` ";
    $sql .= "FROM `" . $iaCore->iaDb->prefix . "locations` ";
    $sql .= "LEFT JOIN `" . $iaCore->iaDb->prefix . "locations_tree` ON `" . $iaCore->iaDb->prefix . "locations`.`id` = `" . $iaCore->iaDb->prefix . 'locations_tree`.`nid` ';
    $sql .= "WHERE `parent` = " . $categoryId;

    $categories = $iaCore->iaDb->getAll($sql);
}

$out = [];

foreach ($categories as $item) {
    $rel = 'default';

    $out[] = [
        'attr' => [
            'id' => $item['id'],
            'rel' => $rel,
            'title' => $item['id'] == $currentCategory ? iaLanguage::get('locked') : '',
        ],
        'cls' => 'folder',
        'data' => $item['title'],
        'state' => 'closed'
    ];
}

$iaView->assign($out);