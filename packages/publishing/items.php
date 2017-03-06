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

$iaArticle = $this->iaCore->factoryPackage('article', $extra);
$catTable = 'articlecats';

if(count($cats) == 1 && $cats[0] == '1')
{
	$where = " AND t1.`date_added` > '" . $last_sent . "' ORDER BY t1.`date_added`";
	$articles = $iaArticle->getArticles($where, 0, $this->iaCore->get('newsletters_items_limit'));
}
else
{
	$all_cats = '';
	$selected_cats = explode(',', $cats);

	foreach($selected_cats as $cat)
	{
		$children = $this->iaCore->iaDb->one('child', '`id` = ' . $cat, $catTable);

		if(!empty($children))
		{
			if ($all_cats !== '')
			{
				$all_cats .= ',';
			}
			$all_cats .= $children;
		}
	}

	$where = " AND t1.`date_added` > '" . $last_sent . "' AND `category_id` IN (" . $all_cats . ") ORDER BY t1.`date_added`";
	$articles = $iaArticle->getArticles($where, 0, $this->iaCore->get('newsletters_items_limit'));
}

$items = array();

if(!empty($articles))
{
	foreach($articles as $key => $article)
	{
		$items[$key]['title'] = $article['title'];
		$items[$key]['description'] = strlen($article['body']) <= $this->iaCore->get('description_chars_num') ?
			$article['body'] : wordwrap($article['body'], $this->iaCore->get('description_chars_num'));
		$items[$key]['url'] = $iaArticle->url('view', $article);
		$items[$key]['date'] = strtotime($article['date_added']);
	}
}