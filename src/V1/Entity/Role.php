<?php

namespace Alfenory\Auth\V1\Entity;

use Doctrine\ORM\Annotation as ORM;

/**
 * user
 * 
 * @ORM\Table(name="auth_role")
 * @ORM\Entity
 **/
class Role implements \JsonSerializable {
    /** @ORM\Id @ORM\Column(type="guid") */
    private $id;
    public function getId() {
        return $this->id;
    }
    
    /** @ORM\Column(type="string", length=255) */
    private $name;
    public function getName() {
        return $name;
    }
    public function setName($name) {
        $this->name = $name;
    }
    
    /** @ORM\Column(type="string") **/
    private $usergroup_id;
    public function getUsergroupId() {
        return $this->usergroup_id;
    }
    public function setUsergroupId($usergroup_id) {
        $this->usergroup_id = $usergroup_id;
    }

    function __construct() {
        $this->id = \Alfenory\Auth\V1\Lib\Guid::guid(); 
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }
}
