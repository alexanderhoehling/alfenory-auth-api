<?php

namespace Alfenory\Auth\V1\Controller;

use Alfenory\Auth\V1\Lib\Returnlib;
use Alfenory\Auth\V1\Lib\Webservicelib;
use Alfenory\Auth\V1\Lib\Sendmail;

class InvitationController {

    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public static function sendInvitation($email, $emailname, $username, $salutation, $seccode) {
        global $config;
        $subject = $config["email"]["content"]["confirmation_subject"];
        $content = $config["email"]["content"]["confirmation"];
        $link = $config["url"]."confirmation/".$seccode;
        $content = preg_replace("/{url}/", $link, $content);
        $content = preg_replace("/{username}/", $username, $content);
        $content = preg_replace("/{salutation]/", $salutation, $content);
        Sendmail::sendEmailFormated($email, $emailname, utf8_encode($subject), utf8_encode($content));
    }

    public static function create($request, $response, $args) {
        global $entityManager;
        if (UserController::has_privileg($request, $response, $args, "ivitation.post")) { 
            $wslib = new Webservicelib();
            $username  = $wslib->filter_string_request($request, "username");
            $email = $wslib->filter_email_request($request, "email");
            $salutation = $wslib->filter_string_request($request, "salutation");
            $firstname = $wslib->filter_string_request($request, "firstname");
            $lastname = $wslib->filter_string_request($request, "lastname");
            $role_id = $wslib->filter_string_request($request, "role_id");
            $route = $request->getAttribute('route');
            $usergroup_id = $route->getArgument('usergroup_id');
            if ($wslib->print_error_if_needed($response) === false) {
                if (UserGroupController::has_usergroup_priv($request, $response, $args, $usergroup_id)) {
                    $invitation = new \Alfenory\Auth\V1\Entity\Invitation();
                    $invitation->setUsername($username);
                    $invitation->setEmail($email);
                    $invitation->setUsergroupId($usergroup_id);
                    $invitation->setSalutation($salutation);
                    $invitation->setFirstName($firstname);
                    $invitation->setLastName($lastname);
                    $invitation->setRoleId($role_id);
                    $invitation->setCreationdate(new \DateTime("now"));
                    $entityManager->persist($invitation);
                    $entityManager->flush();
                    self::sendInvitation($email, $firstname." ".$lastname, $username, $invitation->get_email_salutation(), $invitation->getId());
                    return $response->withJson(Returnlib::get_success());
                }
                else {
                    return $response->withJson(Returnlib::no_privileg());
                }
            } else {
                return $response->withJson(Returnlib::user_parameter_missing($wslib->error_list));
            }
        }
        else {
            return $response->withJson(Returnlib::no_privileg());
        }
    }

    public static function confirmation($request, $response, $args) {
        global $entityManager;
        $wslib = new Webservicelib();
        $username  = $wslib->filter_string_request($request, "username");
        $email = $wslib->filter_email_request($request, "email");
        $salutation = $wslib->filter_string_request($request, "salutation");
        $firstname = $wslib->filter_string_request($request, "firstname");
        $lastname = $wslib->filter_string_request($request, "lastname");
        //CHECK IF USERNAME EXISTS
        
    }

    public static function get($request, $response, $args) {
        global $entityManager;
        if (UserController::has_privileg($request, $response, $args, "invitation.get")) { 
            $route = $request->getAttribute('route');
            $usergroup_id = $route->getArgument('usergroup_id');
            if (UserGroupController::has_usergroup_priv($request, $response, $args, $usergroup_id)) {
                $list = $entityManager->getRepository('Alfenory\Auth\V1\Entity\Invitation')->findBy(array("usergroup_id" => $usergroup_id));
                return $response->withJson(Returnlib::get_success($list));
            } else {
                return $response->withJson(Returnlib::no_privileg());
            }
        } else {
            return $response->withJson(Returnlib::no_privileg());
        }
    }

    public static function delete($request, $response, $args) {
        //TODO
    }

}