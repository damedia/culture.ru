<?php

namespace Armd\TvigleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskProcessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('armd:tvigle:taskProcess')
            ->setDescription('process new video')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $taskList = $em->getRepository('Armd\TvigleBundle\Entity\TvigleTask')->findBy( array() );

        if(!$taskList) {
            $output->writeln('No task');
        } else {
            $optionsPool = $this->getContainer()->get('armd_tvigle.configuration_pool');
            $serviceUrl = $optionsPool->getOption('api_service_url');
            $Login = $optionsPool->getOption('api_login');
            $Password = $optionsPool->getOption('api_password');
            $soap = new \SoapClient
            (
                $serviceUrl,
                array
                (
                    'login'     =>    $Login,
                    'password'  =>    $Password
                )
            );

            $video_url_callback = $this->getContainer()->get('armd_tvigle.configuration_pool')->getOption('video_url_callback');

            foreach($taskList as $task) {
                $output->writeln('Process task '.$task->getId());

                $video = $em->getRepository('Armd\TvigleBundle\Entity\Tvigle')->findOneById( $task->getVideoId() );
                if(!$video) {
                    $output->writeln('Video #'.$task->getVideoId().' not found!');
                    continue;
                }

                $nId = $soap->AddTask
                (
                    $video->getId(),
                    $video->getTitle(),
                    $task->getUrl(),
                    $video_url_callback.$video->getId(),
                    $video->getDescription()
                );

                $video->setTvigleId( $nId );
                $em->persist($video);
                $em->flush();

                $em->remove($task);
                $em->flush();

                $output->writeln('Processed');
            }
        }
    }
}

?>