<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserApiController extends AbstractController {
    private $userRepositorytory;
    private $encoder;

    public function __construct(UserRepository $userRepositorytory, UserPasswordEncoderInterface $encoder) {
        $this->userRepositorytory = $userRepositorytory;
        $this->encoder = $encoder;
    }

    public function delete(Request $request) {
        $data = json_decode($request->get('json'), true);
        if (!empty($data)) {
            $user = $this->userRepositorytory->findOneBy(['id' => (int)$data['id']]);
            if (!empty($user)) {
                try {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->remove($user);
                    $em->flush();
                    return $this->json(['status' => true]);
                } catch (\Exception $ex) {
                }
            }
        }
        return $this->json(['status' => false]);
    }

    public function show(Request $request) {
        $data = json_decode($request->get('json'), true);
        if (!empty($data)) {
            $user = $this->userRepositorytory->findOneBy(['id' => (int)$data['id']]);
            if (!empty($user)) {
                $json['id'] = $user->getId();
                $json['login'] = $user->getLogin();
                $json['email'] = $user->getEmail();
                return $this->json(['status' => true, 'data' => $json]);
            }
        }
        return $this->json(['status' => false]);
    }


    public function edit(Request $request) {
        $data = json_decode($request->get('json'), true);
        if (!empty($data)) {
            $user = $this->userRepositorytory->findOneBy(['id' => (int)$data['id']]);
            if (!empty($user)) {
                $user->setLogin($data['login']);
                $user->setEmail($data['email']);

                $status = $this->saveData($user);
                return $this->json(['status' => $status]);
            }
        }
        return $this->json(['status' => false]);
    }


    public function create(Request $request) {
        $data = json_decode($request->get('json'), true);
        if (!empty($data)) {
            $user = new User();
            $user->setLogin($data['login']);
            $user->setEmail($data['email']);
            $user->setPassword($this->encoder->encodePassword($user, $data['password']));

            $status = $this->saveData($user);
            return $this->json(['status' => $status]);
        }
        return $this->json(['status' => false]);
    }

    private function saveData($user) {
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            return true;
        } catch (\Exception $ex) {
        }
        return false;
    }
}