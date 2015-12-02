<?php
//##copyright##

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