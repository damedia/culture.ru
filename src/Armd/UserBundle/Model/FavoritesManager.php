<?php
namespace Armd\UserBundle\Model;

use Armd\UserBundle\Entity\User;
use Armd\UserBundle\Entity\Favorites;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;

class FavoritesManager {
    protected $securityContext;
    protected $currentUser;
    protected $entityManager;

    public function __construct(SecurityContext $context, EntityManager $em) {
        $this->securityContext = $context;
        $this->currentUser = $context->getToken()->getUser();
        $this->entityManager = $em;
    }

    /**
     * Check if given User is logged in.
     *
     * @param mixed $user
     * @return bool
     */
    private function userLoggedIn($user) {
        return $user instanceof User;
    }

    /**
     * Check if an Entity with given parameters already in Favorites of current User.
     *
     * @param string $entityType
     * @param integer $entityId
     * @return bool
     */
    public function entityIsInFavorites($entityType, $entityId) {
        $user = $this->currentUser;

        if (!$this->userLoggedIn($user)) {
            return false;
        }

        $favorite = $this->entityManager->getRepository('ArmdUserBundle:Favorites')->findOneBy(array(
            'user' => $user->getId(),
            'resourceType' => $entityType,
            'resourceId' => $entityId
        ));

        return $favorite ? true : false;
    }

    /**
     * Add an Entity with given parameters to current User Favorites.
     *
     * @param $entityType
     * @param $entityId
     * @return bool
     */
    public function addToFavorites($entityType, $entityId) {
        $user = $this->currentUser;

        if (!$this->userLoggedIn($user) || $this->entityIsInFavorites($entityType, $entityId)) {
            return false;
        }

        $favorite = new Favorites();
        $favorite->setUser($user);
        $favorite->setResourceType($entityType);
        $favorite->setResourceId($entityId);

        $this->entityManager->persist($favorite);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Remove Entities with given parameters from User Favorites.
     *
     * @param $entityType
     * @param $entityId
     * @return bool
     */
    public function removeFromFavorites($entityType, $entityId) {
        $user = $this->currentUser;

        if (!$this->userLoggedIn($user)) {
            return false;
        }

        $favorites = $this->entityManager->getRepository('ArmdUserBundle:Favorites')->findBy(array(
            'user' => $user->getId(),
            'resourceType' => $entityType,
            'resourceId' => $entityId
        ));

        foreach ($favorites as $favorite) {
            $this->entityManager->remove($favorite);
        }

        $this->entityManager->flush();

        return true;
    }
}
?>