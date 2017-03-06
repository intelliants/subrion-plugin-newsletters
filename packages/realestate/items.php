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

$iaEstate = $this->iaCore->factoryPackage('estate', $extra);
$catTable = 'locations_tree';

if(count($cats) == 1 && $cats[0] == '0')
{
	$where = " `date_added` > '" . $last_sent . "'";
	$estates = $iaEstate->get($where, 0, $this->iaCore->get('newsletters_items_limit'), array('field' => 'date_added', 'direction' => 'ASC'));
}
else
{
	$all_cats = '';
	$selected_cats = explode(',', $cats);

	foreach($selected_cats as $cat)
	{
		$current_cat = $this->iaCore->iaDb->row('`left`, `right`', '`nid` = ' . $cat, $catTable);
		$children = $this->iaCore->iaDb->onefield('nid', '`left` >= ' . $current_cat['left'] . ' AND `right` <= ' . $current_cat['right'], 0, null, $catTable);

		if(!empty($children))
		{
			$children = implode(',', $children);

			if ($all_cats !== '')
			{
				$all_cats .= ',';
			}
			$all_cats .= $children;
		}
	}

	$where = " `date_added` > '" . $last_sent . "' AND `location_id` IN (" . $all_cats . ")";
	$estates = $iaEstate->get($where, 0, $this->iaCore->get('newsletters_items_limit'), array('field' => 'date_added', 'direction' => 'ASC'));
}

$items = array();

if(!empty($estates))
{
	foreach($estates as $key => $estate)
	{
		$items[$key]['title'] = $estate['title'];
		$items[$key]['description'] = strlen($estate['description']) <= $this->iaCore->get('description_chars_num') ?
			$estate['description'] : wordwrap($estate['description'], $this->iaCore->get('description_chars_num'));
		$items[$key]['url'] = $iaEstate->url('view', $estate);
		$items[$key]['date'] = strtotime($estate['date_added']);
	}
}