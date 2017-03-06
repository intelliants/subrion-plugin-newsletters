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

if ($queue = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, null, 'newsletter_recipients'))
{
	$iaDb->setTable('newsletter_messages');

	$iaMailer = $iaCore->factory('mailer');

	$recipients = explode(',', $queue['recipients']);
	$stmt = '`id` = :id AND `active` = :status';
	$iaDb->bind($stmt, array('id' => $queue['message_id'], 'status' => 1));
	if ($m = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, $stmt))
	{
		if ($m['html'])
		{
			$iaMailer->isHTML(true);
		}

		$iaMailer->clearAddresses();
		$iaMailer->FromName = $m['from_name'];
		$iaMailer->From = $m['from_mail'];
		$iaMailer->Subject = $m['subj'];

		foreach($recipients as $email)
		{
			$stmt = '`email` = :email';
			$iaDb->bind($stmt, array('email' => $email));

			if ($subscriber = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, $stmt, 'newsletter_subscribers'))
			{
				$subscriber['fullname'] = $subscriber['fullname'] ? $subscriber['fullname'] : '';
				$mBody = str_replace('{%NEWSLETTERS_TOKEN%}', $subscriber['token'], $iaCore->get('newsletter_send_body_to_subscribers'));
				$mBody = str_replace('{%NEWSLETTERS_CONTENT%}', $m['body'], $mBody);
				$mBody = str_replace('{%FULLNAME%}', $subscriber['fullname'], $mBody);
			}
			else
			{
				$fullname = $iaDb->one('`fullname`', "`email` = '{$email}'", iaUsers::getTable());
				$mBody = str_replace('{%NEWSLETTERS_CONTENT%}', $m['body'], $iaCore->get('newsletter_send_body_to_members'));
				$mBody = str_replace('{%FULLNAME%}', $fullname, $mBody);
			}

			$iaMailer->Body = $mBody;
			$iaMailer->addAddress($email);

			$iaMailer->send();
		}

		$iaDb->delete(iaDb::convertIds($queue['id']), 'newsletter_recipients');

		$iaDb->exists('`message_id` =  ' . $m['id'], null, 'newsletter_recipients')
			? $iaDb->update(null, '`id` = ' . $m['id'], array('total' => '`total` - ' . count($recipients)))
			: $iaDb->delete(iaDb::convertIds($m['id']));
	}

	$iaDb->resetTable();
}