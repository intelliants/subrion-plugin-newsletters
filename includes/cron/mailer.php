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

if ($queue = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, null, 'newsletter_recipients')) {
    $iaDb->setTable('newsletter_messages');

    $iaMailer = $iaCore->factory('mailer');

    $recipients = explode(',', $queue['recipients']);
    $stmt = '`id` = :id AND `active` = :status';
    $iaDb->bind($stmt, ['id' => $queue['message_id'], 'status' => 1]);

    if ($message = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, $stmt)) {
        if ($message['html']) {
            $iaMailer->isHTML(true);
        }

        $iaMailer->clearAddresses();

        $iaMailer->FromName = $message['from_name'];
        $iaMailer->From = $message['from_mail'];

        foreach ($recipients as $email) {
            $stmt = iaDb::convertIds($email, 'email');

            if ($subscriber = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, $stmt, 'newsletter_subscribers')) {
                $iaMailer->loadTemplate('newsletter_send_subscribers');
                $iaMailer->setReplacements([
                    'fullname' => $subscriber['fullname'] ? $subscriber['fullname'] : '',
                    'newslettersToken' => $subscriber['token']
                ]);
            } else {
                $iaMailer->loadTemplate('newsletter_send_members');
                $iaMailer->setReplacements([
                    'fullname' => $iaDb->one('`fullname`', "`email` = '{$email}'", iaUsers::getTable()),
                ]);
            }

            $iaMailer->setSubject($message['subj']);
            $iaMailer->setReplacements('newslettersContent', $message['body'], true);

            $iaMailer->addAddress($email);

            $iaMailer->send();
        }

        $iaDb->delete(iaDb::convertIds($queue['id']), 'newsletter_recipients');

        $iaDb->exists('`message_id` =  ' . $message['id'], null, 'newsletter_recipients')
            ? $iaDb->update(null, iaDb::convertIds($message['id']), ['total' => '`total` - ' . count($recipients)])
            : $iaDb->delete(iaDb::convertIds($message['id']));
    }

    $iaDb->resetTable();
}