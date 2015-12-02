<?php
//##copyright##

class iaBackendController extends iaAbstractControllerPluginBackend
{
	protected $_name = 'subscribers';

	protected $_table = 'newsletter_subscribers';

	protected $_gridColumns = array('fullname', 'email', 'date', 'status');
	protected $_gridFilters = array('email' => self::LIKE, 'fullname' => self::LIKE, 'status' => self::EQUAL);

	protected $_processEdit = false;


	public function init()
	{
		$this->_template = 'subscribers-add';
		$this->_path = IA_ADMIN_URL . $this->getPluginName() . IA_URL_DELIMITER . $this->getName() . IA_URL_DELIMITER;

		if (iaView::REQUEST_HTML == $this->_iaCore->iaView->getRequestType())
		{
			iaBreadcrumb::insert(iaLanguage::get('newsletter'), IA_ADMIN_URL . 'newsletters/', iaBreadcrumb::POSITION_FIRST + 1);
		}
	}

	protected function _setDefaultValues(array &$entry)
	{
		$entry['email'] = '';
		$entry['fullname'] = '';
		$entry['status'] = iaCore::STATUS_ACTIVE;
	}

	protected function _preSaveEntry(array &$entry, array $data, $action)
	{
		parent::_preSaveEntry($entry, $data, $action);

		if (empty($entry['email']))
		{
			$this->addMessage(iaLanguage::getf('field_is_empty', array('field' => iaLanguage::get('email'))), false);
		}
		elseif (!iaValidate::isEmail($entry['email']))
		{
			$this->addMessage('error_email_incorrect');
		}
		elseif ($this->_iaDb->exists(iaDb::convertIds($entry['email'], 'email')))
		{
			$this->addMessage('subscriber_email_exists');
		}

		return !$this->getMessages();
	}

	protected function _entryAdd(array $entryData)
	{
		$entryData['date'] = date(iaDb::DATETIME_FORMAT);

		return parent::_entryAdd($entryData);
	}
}