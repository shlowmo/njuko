<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    public function listAction()
    {
        //Get sort options
        $field = $this->params()->fromRoute('field');
        $type = $this->params()->fromRoute('type');
        
        if($field&&$type) {
            $users = $this->getServiceLocator()->get('entity_manager')
            ->getRepository('Application\Entity\User')
            ->findBy(array(), array($field => $type));
        }
        else {
            $users = $this->getServiceLocator()->get('entity_manager')
                ->getRepository('Application\Entity\User')
                ->findAll();
        }
        
        return new ViewModel(array(
            'users' =>  $users,
            'field' => $field,
            'type' => $type
        ));
    }

    public function addAction()
    {
        /* @var $form \Application\Form\UserForm */
        $form = $this->getServiceLocator()->get('formElementManager')->get('form.user');

        $data = $this->prg();

        if ($data instanceof \Zend\Http\PhpEnvironment\Response) {
            return $data;
        }

        if ($data != false) {
            $form->setData($data);
            if ($form->isValid()) {

                /* @var $user \Application\Entity\User */
                $user = $form->getData();

                /* @var $serviceUser \Application\Service\UserService */
                $serviceUser = $this->getServiceLocator()->get('application.service.user');

                $serviceUser->saveUser($user);

                $this->redirect()->toRoute('users');
            }
        }

        return new ViewModel(array(
            'form'  =>  $form
        ));
    }

    public function removeAction()
    {
        //To do : Do Remove User
        $userTodelete = $this->getServiceLocator()->get('entity_manager')
            ->getRepository('Application\Entity\User')
            ->find($this->params()->fromRoute('user_id'));
        
        if(!$userTodelete){
            //Add some error message
            $this->redirect()->toRoute('users');
        }
        $serviceUser = $this->getServiceLocator()->get('application.service.user');
        $serviceUser->removeUser($userTodelete);
        
        $this->redirect()->toRoute('users');
    }

    public function editAction()
    {
        /* @var $form \Application\Form\UserForm */
        $form = $this->getServiceLocator()->get('formElementManager')->get('form.user');

        $userToEdit = $this->getServiceLocator()->get('entity_manager')
            ->getRepository('Application\Entity\User')
            ->find($this->params()->fromRoute('user_id'));

        $form->bind($userToEdit);
        $form->get('firstname')->setValue($userToEdit->getFirstname());
        $form->get('lastname')->setValue($userToEdit->getLastname());
        $form->get('address')->setValue($userToEdit->getAddress());
        $form->get('birthdate')->setValue($userToEdit->getBirthdate());

        $data = $this->prg();

        if ($data instanceof \Zend\Http\PhpEnvironment\Response) {
            return $data;
        }

        if ($data != false) {
            $form->setData($data);
            if ($form->isValid()) {

                /* @var $user \Application\Entity\User */
                $user = $form->getData();

                //Save the user
                $serviceUser = $this->getServiceLocator()->get('application.service.user');
                $serviceUser->saveUser($user);
                $this->redirect()->toRoute('users');
            }
        }

        return new ViewModel(array(
            'form'  =>  $form
        ));
    }

}