<?php
//##copyright##

class iaQueue extends abstractPlugin
{
	const PLUGIN_NAME = 'newsletters';

	const RCPTS_PER_RUN = 15;

	protected static $_tableMessages = 'newsletter_messages';
	protected static $_tableRecipients = 'newsletter_recipients';
	protected static $_tableSubscribers = 'newsletter_subscribers';
	protected static $_tableMessagesArchive = 'newsletter_messages_archive';


	// table name getters
	public static function getTableMessages()
	{
		return self::$_tableMessages;
	}

	public static function getTableRecipients()
	{
		return self::$_tableRecipients;
	}

	public static function getTableSubscribers()
	{
		return self::$_tableSubscribers;
	}

	public static function getTableMessagesArchive()
	{
		return self::$_tableMessagesArchive;
	}

	public function create($fromName, $fromMail, $subject, $body, $html, $groups, $subscribers, $status)
	{
		$error = false;
		$messages = array();

		$data = array(
			'from_name' => $fromName,
			'subj' => $subject,
			'html' => $html
		);

		if (empty($fromMail) || !iaValidate::isEmail($fromMail))
		{
			$error = true;
			$messages[] = iaLanguage::get('from_email_err');
		}
		else
		{
			$data['from_mail'] = $fromMail;
		}

		if (empty($body))
		{
			$error = true;
			$messages[] = iaLanguage::get('err_message');
		}
		else
		{
			$data['body'] = $body;
		}

		$rcpt = $this->_getEmails($groups, $status);

		if ($subscribers)
		{
			$subscrbr = $this->iaDb->onefield('email', null, 0, 0, self::getTableSubscribers());
			$rcpt = array_merge($rcpt, $subscrbr);
			$rcpt = array_unique($rcpt);
		}

		if (empty($rcpt))
		{
			$error = true;
			$messages[] = iaLanguage::get('no_rcpt');
		}

		if (!$error)
		{
			$data['total'] = count($rcpt);

			$messageId = $this->iaDb->insert($data, null, self::getTableMessages());

			//save for archive
			$data['date_added'] = date(iaDb::DATETIME_FORMAT);
			$this->iaDb->insert($data, null, self::getTableMessagesArchive());

			foreach ($rcpt as $index => $addr)
			{
				$rcptCart[] = $addr;

				if (($index + 1) % self::RCPTS_PER_RUN == 0 || $index + 1 == $data['total'])
				{
					$this->iaDb->insert(array('message_id' => $messageId, 'recipients' => implode(',', $rcptCart)), null, self::getTableRecipients());
					$rcptCart = array();
				}
			}

			$messages[] = iaLanguage::get('queue_added');
		}

		return array($error, $messages);
	}

	public function get()
	{
		return $this->iaDb->all(array('id', 'subj', 'active', 'total'), iaDb::EMPTY_CONDITION, null, null, self::getTableMessages());
	}

	public function toggle($id)
	{
		$this->iaDb->update(null, iaDb::convertIds($id), array('active' => 'IF (1 <> `active`, 1, 0)'), self::getTableMessages());
	}

	public function delete($id)
	{
		$this->iaDb->delete(iaDb::convertIds($id), self::getTableMessages());
		$this->iaDb->delete(iaDb::convertIds($id, 'message_id'), self::getTableRecipients());
	}

	protected function _getEmails($userGroups, $statuses)
	{
		$groupIds = array();

		if (is_array($userGroups) && $userGroups)
		{
			foreach ($userGroups as $groupId)
			{
				$groupIds[] = (int)$groupId;
			}
		}

		$where = iaDb::EMPTY_CONDITION;

		empty($groupIds) || $where.= ' AND `usergroup_id` IN (' . implode(',', $groupIds) . ')';

		if (is_array($statuses))
		{
			$statuses = array_map(array('iaSanitize', 'paranoid'), $statuses);
			$where.= " AND `status` IN('" . implode("','", $statuses) . "')";
		}

		return $this->iaDb->onefield('email', $where, null, null, iaUsers::getTable());
	}
}
