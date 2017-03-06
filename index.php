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

$iaNewsletter = $iaCore->factoryPlugin(IA_CURRENT_MODULE);

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	if (isset($_GET['subscriber_email']))
	{
		$response = array(
			'error' => true
		);

		$fullname = iaSanitize::tags($_GET['subscriber_fullname']);
		$email = $_GET['subscriber_email'];

		if (!iaValidate::isEmail($email))
		{
			$response['message'] = iaLanguage::get('error_email_incorrect');
		}
		elseif ($iaNewsletter->emailExists($email))
		{
			$response['message'] = iaLanguage::get('subscriber_email_exists');
		}
		else
		{
			$iaUtil = $iaCore->factory('util');

			$data = array(
				'status' => iaCore::STATUS_INACTIVE,
				'date' => date(iaDb::DATETIME_FORMAT),
				'email' => $email,
				'fullname' => $fullname ? $fullname : null,
				'token' => iaUtil::generateToken(32)
			);

			$iaNewsletter->insert($data);

			$iaMailer = $iaCore->factory('mailer');

			$iaMailer->ClearAddresses();
			$iaMailer->FromName = $iaCore->get('site_from_name', 'Subrion CMS');
			$iaMailer->From = $iaCore->get('site_email');
			$iaMailer->AddAddress($data['email']);
			$iaMailer->Subject = $iaCore->get('newsletter_subscription_subject');
			$iaMailer->Body = $iaCore->get('newsletter_subscription_body');
			$iaMailer->setReplacements(array(
				'newsletters_token' => $data['token'],
				'newsletters_email' => $data['email']
			));
			$iaMailer->Send();

			$response['error'] = false;
			$response['message'] = iaLanguage::get('subscription_email_sent');
		}

		$iaView->assign($response);
	}
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$error = false;
	$messages = array();

	if ($_GET['subscribe'])
	{
		if ($iaNewsletter->emailConfirmation($_GET['email'], $_GET['subscribe']))
		{
			$messages[] = iaLanguage::get('newsletters_confirmed');
		}
		else
		{
			$error = true;
			$messages[] = iaLanguage::get('newsletters_confirmation_code_incorrect');
		}
	}

	if (isset($_GET['unsubscribe']))
	{
		if ($iaNewsletter->tokenExists($_GET['unsubscribe']))
		{
			$iaNewsletter->delete($_GET['unsubscribe']);
			$error = true;
			$messages[] = iaLanguage::get('newsletters_unsubscribed');
		}
		else
		{
			return iaView::errorPage(iaView::ERROR_NOT_FOUND);
		}
	}
	$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);

	$iaView->display();
}