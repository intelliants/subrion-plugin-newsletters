<?php
//##copyright##

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