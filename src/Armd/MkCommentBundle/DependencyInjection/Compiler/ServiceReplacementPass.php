<?php
namespace Armd\MkCommentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceReplacementPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        // functionality of these services are replaced by CommentListener service
        $container->removeDefinition('fos_comment.listener.thread_counters');
        $container->removeDefinition('fos_comment.listener.comment_blamer');
        $container->removeDefinition('');
    }
}