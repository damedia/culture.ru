<?php

namespace Armd\LectureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Table(name="lecture_role_person")
* @ORM\Entity
*/
class LectureRolePerson
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\LectureBundle\Entity\Lecture", cascade={"persist"}, inversedBy="rolesPersons")
     * @ORM\JoinColumn(name="lecture_id", referencedColumnName="id")
     */
    private $lecture;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\LectureBundle\Entity\LectureRole", cascade={"persist"})
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\LectureBundle\Entity\LecturePerson", cascade={"persist"})
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $person;

    public function __toString()
    {
        return (string) $this->getPerson()->getName().', '.$this->getRole()->getName();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set role
     *
     * @param \Armd\LectureBundle\Entity\LectureRole $role
     * @return LectureRolePerson
     */
    public function setRole(\Armd\LectureBundle\Entity\LectureRole $role = null)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return \Armd\LectureBundle\Entity\LectureRole 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set person
     *
     * @param \Armd\LectureBundle\Entity\LecturePerson $person
     * @return LectureRolePerson
     */
    public function setPerson(\Armd\LectureBundle\Entity\LecturePerson $person = null)
    {
        $this->person = $person;
    
        return $this;
    }

    /**
     * Get person
     *
     * @return \Armd\LectureBundle\Entity\LecturePerson 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set lecture
     *
     * @param \Armd\LectureBundle\Entity\Lecture $lecture
     * @return LectureRolePerson
     */
    public function setLecture(\Armd\LectureBundle\Entity\Lecture $lecture = null)
    {
        $this->lecture = $lecture;
    
        return $this;
    }

    /**
     * Get lecture
     *
     * @return \Armd\LectureBundle\Entity\Lecture 
     */
    public function getLecture()
    {
        return $this->lecture;
    }
}