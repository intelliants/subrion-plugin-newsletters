<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2015 Intelliants, LLC <http://www.intelliants.com>
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
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

$iaCoupons = $this->iaCore->factoryPackage('coupon', $extra);
$catTable = 'coupons_categories';

if (count($cats) == 1 && $cats[0] == '0')
{
	$where = " t1.`date_added` > '" . $last_sent;
	$listings = $iaCoupons->getCoupons($where, 't1.`date_added` DESC', $this->iaCore->get('newsletters_items_limit'), 0);
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

	$where = " t1.`date_added` > '" . $last_sent . "' AND `category_id` IN (" . $all_cats . ")";
	$listings = $iaCoupons->getCoupons($where, 't1.`date_added` DESC', $this->iaCore->get('newsletters_items_limit'), 0);
}

$items = array();

if(!empty($listings))
{
	foreach($listings as $key => $listing)
	{
		$items[$key]['title'] = $listing['title'];
		$items[$key]['description'] = strlen($listing['description']) <= $this->iaCore->get('description_chars_num') ?
			$listing['description'] : wordwrap($listing['description'], $this->iaCore->get('description_chars_num'));
		$items[$key]['url'] = $iaCoupons->url('view', $listing);
		$items[$key]['date'] = strtotime($listing['date_added']);
	}
}