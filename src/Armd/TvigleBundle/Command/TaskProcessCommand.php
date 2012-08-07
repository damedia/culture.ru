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
            $params = $this->getContainer()->getParameter('tvigle');
            $serviceUrl = $params['api_service_url'];
            $Login = $params['api_login'];
            $Password = $params['api_password'];
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
                \gFuncs::dbgWriteLogVar($video->getId(), false, 'AddTask id'); // DBG:
                \gFuncs::dbgWriteLogVar($video->getTitle(), false, 'AddTask title'); // DBG:
                \gFuncs::dbgWriteLogVar($task->getUrl(), false, 'AddTask url'); // DBG:
                \gFuncs::dbgWriteLogVar($video_url_callback.$video->getId(), false, 'AddTask url1'); // DBG:
                \gFuncs::dbgWriteLogVar($nId, false, 'AddTask nId'); // DBG:

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