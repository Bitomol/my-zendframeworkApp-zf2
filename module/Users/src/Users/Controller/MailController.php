<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Mail;


class MailController  extends AbstractActionController {

    protected $storage;
    protected $authservice;

    protected function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }

        return $this->authservice;
    }

    protected function getLoggedInUser()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $userEmail = $this->getAuthService()->getStorage()->read();
        $user = $userTable->getUserByEmail($userEmail);

        return $user;
    }

    public function indexAction()
    {
        $this->layout('layout/myaccount');

        $this->layout()->setVariable('mail_active', 'active');

        $userTable = $this->getServiceLocator()->get('UserTable');
        $allUsers = $userTable->fetchAll();
        $usersList = array();
        foreach($allUsers as $user) {
            $usersList[$user->id] = $user->name . '(' . $user->email . ')';
        }

        $user = $this->getLoggedInUser();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $msgSubj = $request->getPost()->get('messageSubject');
            $msgText = $request->getPost()->get('message');
            $toUser = $request->getPost()->get('toUserId');
            $fromUser = $user->id;
            $this->sendOfflineMessage($msgSubj, $msgText, $fromUser, $toUser);
            // to prevent duplicate entries on refresh
            return $this->redirect()->toRoute('users/mail',
                array('action' => 'sendOfflineMessage'));
        }

        //Prepare Send Message Form
        $form    = new \Zend\Form\Form();
        $form->setAttribute('method', 'post');
        $form->setAttribute('enctype','multipart/form-data');

        $form->add(array(
            'name' => 'toUserId',
            'type'  => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
            ),
            'options' => array(
                'label' => 'To User',
            ),
        ));

        $form->add(array(
            'name' => 'messageSubject',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'messageSubject',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Subject',
            ),
        ));

        $form->add(array(
            'name' => 'message',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'message',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Message',
            ),
        ));

        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Send'
            ),
            'options' => array(
                'label' => 'Send',
            ),
        ));

        $form->get('toUserId')->setValueOptions($usersList);
        $viewModel  = new ViewModel(array('form' => $form,
            'userName' => $user->name));
        return $viewModel;
    }

    protected function sendOfflineMessage($msgSubj, $msgText, $fromUserId, $toUserId)
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $fromUser = $userTable->getUser($fromUserId);
        $toUser = $userTable->getUser($toUserId);

        $mail = new Mail\Message();
        $mail->setFrom($fromUser->email, $fromUser->name);
        $mail->addTo($toUser->email, $toUser->name);
        $mail->setSubject($msgSubj);
        $mail->setBody($msgText);

        $transport = new Mail\Transport\Sendmail();
        $transport->send($mail);

        return true;
    }

}