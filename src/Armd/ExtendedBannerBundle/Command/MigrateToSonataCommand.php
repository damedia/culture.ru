<?php

/*
 * This file is part of the Sonata package.
*
* (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Armd\ExtendedBannerBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MigrateToSonataCommand extends ContainerAwareCommand
{
    protected $quiet = false;
    protected $output;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('armd:banner:migrate-to-sonata')
            ->setDescription('Migrate old banners to the sonata media')
            ->setDefinition(array(
                new InputArgument('context', InputArgument::REQUIRED, 'The context'),
                
                new InputOption('entity', null, InputOption::VALUE_OPTIONAL, 'Full entity name', 'Armd\ExtendedBannerBundle\Entity\BaseBanner'),
                new InputOption('uploads', null, InputOption::VALUE_OPTIONAL, 'Full path to uploaded images dir', realpath(__DIR__.'/../../../../web/').'/uploads/banners'),
                new InputOption('provider', null, InputOption::VALUE_OPTIONAL, 'The provider', 'sonata.media.provider.image'),
                new InputOption('description', null, InputOption::VALUE_OPTIONAL, 'The media description field', null),
                new InputOption('copyright', null, InputOption::VALUE_OPTIONAL, 'The media copyright field', null),
                new InputOption('author', null, InputOption::VALUE_OPTIONAL, 'The media author name field', null),
                new InputOption('enabled', null, InputOption::VALUE_OPTIONAL, 'The media enabled field', true),
                new InputOption('vv', null, InputOption::VALUE_OPTIONAL, 'Toggle verbose output', true),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $context    = $input->getArgument('context');
        $provider   = $input->getOption('provider');
        $entity     = $input->getOption('entity');
        $uploadPath = $input->getOption('uploads');
        $verbose    = $input->getOption('vv');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository($entity);
        
        $list = $repo->createQueryBuilder('b')
                ->select('b')
                ->where('b.image IS NULL')
                ->orderBy('b.id', 'ASC')
                ->getQuery()->getResult();
        
        $total = sizeof($list);
        $output->writeln(sprintf("Migrating %s -> context: %s, provider: %s", $entity, $context, $provider));
        foreach($list as $i => $model) {
            if(($imgPath = $model->getImagePath())) {
                try {
                    $imgPath = $uploadPath.'/'.$imgPath;
                    $media = $this->getMediaManager()->create();
                    $media->setBinaryContent($imgPath);

                    if ($input->getOption('description')) {
                        $media->setDescription($input->getOption('description'));
                    }
                    if ($input->getOption('copyright')) {
                        $media->setCopyright($input->getOption('copyright'));
                    }
                    if ($input->getOption('author')) {
                        $media->setAuthorName($input->getOption('author'));
                    }
                    $media->setEnabled(in_array($input->getOption('enabled'), array(1, true, 'true'), true));

                    $this->getMediaManager()->save($media, $context, $provider);
                    $model->setImage($media);
                    $em->persist($model);
                    unlink($imgPath);
                    
                    if($verbose) {
                        $output->writeln(sprintf("\t%s/%s\tID:%s \t done", $i+1, $total, $model->getId()));
                    }
                } catch(\Exception $e) {
                    if($verbose) {
                        $output->writeln(sprintf("\t%s/%s\tID:%s \t failed \t %s", $i+1, $total, $model->getId(), $e->getMessage()));
                    }
                }
            }
        }
        $em->flush();
        $output->writeln("Complete!");
    }
    
    /**
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getMediaManager()
    {
        return $this->getContainer()->get('sonata.media.manager.media');
    }

    /**
     * @return \Sonata\MediaBundle\Provider\Pool
     */
    public function getMediaPool()
    {
        return $this->getContainer()->get('sonata.media.pool');
    }
}
