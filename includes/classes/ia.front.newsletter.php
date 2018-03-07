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

class iaNewsletter extends abstractCore
{
    protected static $_table = 'newsletter_subscribers';


    public function emailExists($email)
    {
        return $this->iaDb->exists('`email` = :email', array('email' => $email), self::getTable());
    }

    public function tokenExists($token)
    {
        return $this->iaDb->exists('`token` = :token', array('token' => $token), self::getTable());
    }

    public function insert($data)
    {
        $this->iaDb->insert($data, null, self::getTable());
    }

    public function update($data, $where, $addit)
    {
        $this->iaDb->update($data, $where, $addit, self::getTable());
    }

    public function delete($token)
    {
        $this->iaDb->delete('`token` = :token', self::getTable(), ['token' => $token]);
    }

    public function generateLetterContent($topics, $last_sent)
    {
        $available_packages = [];

        foreach($this->iaCore->packagesData as $package)
            $available_packages[] = $package['name'];

        $html = '';

        $topics = explode('|', $topics);

        foreach($topics as $topic) {
            $topic = explode(':', $topic);
            $extra = $topic[0];

            if (in_array($extra, $available_packages)) {
                include 'plugins/newsletters/packages/' . $extra . '/items.php';

                if (!empty($items)) {
                    $html .= '<strong><em><p style="font-size:22px;text-transform:capitalize;">' . $this->iaCore->packagesData[$extra]['title'] . '</p></em></strong><br />';
                    foreach ($items as $item) {
                        $html .= '<p style="font-size:16px;"><strong><a href="' . $item['url'] . '">' . $item['title']
                            . '</a></strong> - <em>' . date('F j, Y', $item['date']) . '</em></p>';
                        $html .= '<span style="font-size:14px;">' . $item['description'] . '</span><br /><br />';
                    }
                }
            }
        }

        return $html;
    }

    public function emailConfirmation($email, $token)
    {
        $stmt = '`email` = :email AND `token` = :token';
        $this->iaDb->bind($stmt, array('email' => $email, 'token' => $token));

        return (bool)$this->iaDb->update(['status' => iaCore::STATUS_ACTIVE], $stmt, null, self::getTable());
    }
}