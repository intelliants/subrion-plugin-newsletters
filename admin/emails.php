<?php
//##copyright##

class iaBackendController extends iaAbstractControllerPluginBackend
{
	protected $_name = 'emails';

	protected $_table = 'newsletter_messages_archive';

	protected $_gridColumns = array('from_name', 'subj', 'body', 'total', 'date_added');
	protected $_gridFilters = array('status' => self::EQUAL);

	protected $_processAdd = false;
	protected $_processEdit = false;


	public function init()
	{
		if (iaView::REQUEST_HTML == $this->_iaCore->iaView->getRequestType())
		{
			iaBreadcrumb::insert(iaLanguage::get('newsletter'), IA_ADMIN_URL . 'newsletters/', iaBreadcrumb::POSITION_FIRST + 1);
		}
	}
}