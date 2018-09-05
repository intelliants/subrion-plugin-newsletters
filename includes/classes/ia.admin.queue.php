<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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

class iaQueue extends abstractModuleAdmin
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
        $messages = [];

        $data = [
            'from_name' => $fromName,
            'subj' => $subject,
            'html' => $html
        ];

        if (empty($fromMail) || !iaValidate::isEmail($fromMail)) {
            $error = true;
            $messages[] = iaLanguage::get('error_email_incorrect');
        } else {
            $data['from_mail'] = $fromMail;
        }

        if (empty($body)) {
            $error = true;
            $messages[] = iaLanguage::get('err_message');
        } else {
            $data['body'] = $body;
        }

        $rcpt = $this->_getEmails($groups, $status);

        if ($subscribers) {
            $subscrbr = $this->iaDb->onefield('email', null, 0, 0, self::getTableSubscribers());
            $rcpt = array_merge($rcpt, $subscrbr);
            $rcpt = array_unique($rcpt);
        }

        if (empty($rcpt)) {
            $error = true;
            $messages[] = iaLanguage::get('no_recipients');
        }

        if (!$error) {
            $data['total'] = count($rcpt);

            $messageId = $this->iaDb->insert($data, null, self::getTableMessages());

            //save for archive
            $data['date_added'] = date(iaDb::DATETIME_FORMAT);
            $this->iaDb->insert($data, null, self::getTableMessagesArchive());

            foreach ($rcpt as $index => $addr) {
                $rcptCart[] = $addr;

                if (($index + 1) % self::RCPTS_PER_RUN == 0 || $index + 1 == $data['total']) {
                    $this->iaDb->insert(['message_id' => $messageId, 'recipients' => implode(',', $rcptCart)], null, self::getTableRecipients());
                    $rcptCart = [];
                }
            }

            $messages[] = iaLanguage::get('queue_added');
        }

        return [$error, $messages];
    }

    public function get()
    {
        return $this->iaDb->all(['id', 'subj', 'active', 'total'], iaDb::EMPTY_CONDITION, null, null, self::getTableMessages());
    }

    public function toggle($id)
    {
        $this->iaDb->update(null, iaDb::convertIds($id), ['active' => 'IF (1 <> `active`, 1, 0)'], self::getTableMessages());
    }

    public function delete($id)
    {
        $this->iaDb->delete(iaDb::convertIds($id), self::getTableMessages());
        $this->iaDb->delete(iaDb::convertIds($id, 'message_id'), self::getTableRecipients());
    }

    protected function _getEmails($userGroups, $statuses)
    {
        $groupIds = [];

        if (is_array($userGroups) && $userGroups) {
            foreach ($userGroups as $groupId) {
                $groupIds[] = (int)$groupId;
            }
        }

        $where = iaDb::EMPTY_CONDITION;

        empty($groupIds) || $where.= ' AND `usergroup_id` IN (' . implode(',', $groupIds) . ')';

        if (is_array($statuses)) {
            $statuses = array_map(['iaSanitize', 'paranoid'], $statuses);
            $where.= " AND `status` IN('" . implode("','", $statuses) . "')";
        }

        return $this->iaDb->onefield('email', $where, null, null, iaUsers::getTable());
    }
}
