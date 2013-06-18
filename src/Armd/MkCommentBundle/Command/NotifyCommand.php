<?php

namespace Armd\MkCommentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class NotifyCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('armd-mk:comment:notify')
            ->setDescription('Send notifies to users about new comments')
            ->setHelp('');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userNotices = $this->findNotices();
        foreach($userNotices as $notices){
            $entities = $this->getContainer()->get('doctrine')->getEntityManager()->getRepository('ArmdMkCommentBundle:Notice')
                ->createQuerybuilder('n')
                ->select('n')
                ->join('n.user', 'u')
                ->join('n.comment', 'c')
                ->join('c.thread', 't')
                ->where('n.id IN (:ids)')
                ->setParameter('ids', array_values($notices))
                ->getQuery()
                ->getResult();
            
            foreach($entities as $notice){
                $notice->getComment()->setThreadCrumbs($this->getContainer()
                                                            ->get('fos_comment.manager.comment')
                                                            ->getThreadCrumbsByComment($notice->getComment())
                                                        );
            }
            
            $this->send($entities);
        }
    }

    /**
     * Finds pending notices, groups them by user and filters by thread
     * @return array
     */
    private function findNotices()
    {
        $start = new \DateTime();
        $start->setTime(0, 0, 0);
        $start->sub(new \DateInterval('P1D'));
        
        $end = new \DateTime();
        $end->setTime(23, 59, 59);
        $end->sub(new \DateInterval('P1D'));

        $res = $this->getContainer()->get('doctrine')->getEntityManager()->getRepository('ArmdMkCommentBundle:Notice')
                ->createQuerybuilder('n')
                ->select('n.id, IDENTITY(n.user) as userid, IDENTITY(c.thread) as threadId')
                ->join('n.user', 'u')
                ->join('n.comment', 'c')
                ->join('c.thread', 't')
                ->where('n.createdAt BETWEEN :start AND :end')
                ->orderBy('n.createdAt', 'ASC')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getScalarResult();
        $notices = array();
        foreach($res as $r){
            if(!isset($notices[$r['userid']])){
                $notices[$r['userid']] = array();
            }
            if(!isset($notices[$r['userid']][$r['threadId']])){
                $notices[$r['userid']][$r['threadId']] = $r['id'];
            }
        }

        return $notices;
    }

    /**
     * @param array $notices
     */
    private function send($notices)
    {
        $user = $notices[0]->getUser();
        $locale = $this->getLocale($notices[0]);
        $this->getContainer()->get('translator')->setLocale($locale);
        $mailSubject = $this->getContainer()->get('translator')->trans('Comment notifications', array(), 'ArmdMkCommentBundle', $locale);
        $mailBody = $this->getContainer()->get('templating')->render('ArmdMkCommentBundle:Email:notifyUserMessage.html.twig', array('entities' => $notices));

        $mail = new \Swift_Message();
        $mail->setFrom('no-reply@culture.ru');
        $mail->setSubject($mailSubject);
        $mail->setBody($mailBody, 'text/html');
        $mail->setTo($user->getEmail());

        $this->getContainer()->get('mailer')->send($mail);
    }
    
    private function getLocale(\Armd\MkCommentBundle\Entity\Notice $notice)
    {
        $vars = explode('_', $notice->getComment()->getThread()->getId(), 3);
        return $vars[0];
    }
}
