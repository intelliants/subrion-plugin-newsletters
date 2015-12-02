<?php
//##copyright##

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