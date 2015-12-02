<?php
//##copyright##

$iaNewsletter = $iaCore->factoryPlugin(IA_CURRENT_PLUGIN, iaCore::FRONT, 'newsletter');

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