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