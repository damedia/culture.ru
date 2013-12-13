<?php
namespace Armd\MkCommentBundle\Model;

class CommentsIntegrator {
    /**
     * Check if an Entity implements Commentable interface.
     *
     * @param mixed $entity
     * @return bool
     */
    public function entityIsCommentable($entity) {
        $interfaces = class_implements(get_class($entity));

        return (isset($interfaces['Armd\MkCommentBundle\Model\CommentableInterface'])) ? true : false;
    }
}
?>