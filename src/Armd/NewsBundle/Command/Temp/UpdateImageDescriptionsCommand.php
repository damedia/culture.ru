<?php

namespace Armd\NewsBundle\Command\Temp;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Armd\AtlasBundle\Entity\Object;
use Armd\MuseumBundle\Entity\Museum;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class UpdateImageDescriptionsCommand extends DoctrineCommand
{
    protected function configure()
    {

        $this
            ->setName('armd-mk:news:update-image-descriptions')
            ->setDescription('')
            ->setHelp(
            <<<EOT
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');

        $news = $em->getRepository('ArmdNewsBundle:News')->findAll();
        $newsManager = $this->getContainer()->get('armd_news.manager.news');

        foreach ($news as $article) {
            $output->writeln('Update description for news article ' . $article->getId());
            $newsManager->updateImageDescription($article);
        }
        $em->flush();
    }


}