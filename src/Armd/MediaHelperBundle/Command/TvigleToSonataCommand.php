<?php
namespace Armd\MediaHelperBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Application\Sonata\MediaBundle\Entity\Media;

class TvigleToSonataCommand  extends DoctrineCommand {
    /**
     * Relations types
     *
     * @var array
     */
    protected $relationsTypes = array(
        ClassMetadataInfo::ONE_TO_MANY  => 'OneToMany',
        ClassMetadataInfo::MANY_TO_ONE  => 'ManyToOne',
        ClassMetadataInfo::MANY_TO_MANY => 'ManyToMany'
    );

    /**
     * Media context map.
     *
     * @var array
     */
    protected $contextMap = array(
        'Armd\AtlasBundle\Entity\Object'          => 'atlas',
        'Armd\NewsBundle\Entity\News'             => 'news',
        'Armd\LectureBundle\Entity\Lecture'       => 'lecture',
        'Armd\ExhibitBundle\Entity\ArtObject'     => 'exhibit',
        'Armd\TheaterBundle\Entity\Theater'       => 'theater',
        'Armd\PerfomanceBundle\Entity\Perfomance' => 'perfomance',
        'Armd\TouristRouteBundle\Entity\Route'    => 'route'
    );

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('armd-mk:media:tvigle-to-sonata')
            ->setDescription('Moves entities Tvigle relations to SonataMedia.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        foreach ($metadata as $entityMetadata) {
            $firstMatch = true;

            foreach ($entityMetadata->associationMappings as $key=>$val) {
                if ($val['targetEntity'] == 'Armd\TvigleVideoBundle\Entity\TvigleVideo') {
                    $fromFieldName = $key;
                    $toFieldName = 'media' .ucfirst($key);

                    if (
                        isset($entityMetadata->associationMappings[$toFieldName]) and
                        $entityMetadata->associationMappings[$toFieldName]['targetEntity'] == 'Application\Sonata\MediaBundle\Entity\Media'
                    ) {
                        if ($firstMatch) {
                            $output->writeln("\n<fg=magenta>{$entityMetadata->name}</fg=magenta>");
                            $firstMatch = false;
                        }
                        
                        $fromField  = $val;
                        $toField    = $entityMetadata->associationMappings[$toFieldName];
                        $typesMatch = ($fromField['type'] == $toField['type']);

                        if ($typesMatch) {
                            $output->write("    From <fg=green>{$fromFieldName}</fg=green> to <fg=green>{$toFieldName}</fg=green>: ");

                            $count = $this->convert($entityMetadata->name, $fromField, $toField);

                            if ($count['success'] or $count['errors']) {
                                $output->writeln("<fg=green>{$count['success']}</fg=green> item(s) successfully converted. " .($count['errors'] ? "<fg=red>{$count['errors']}</fg=red> error(s)." : ""));

                            } else {
                                $output->writeln("Nothing to convert.");
                            }

                        } else {
                            $output->writeln("\tFrom <fg=red>{$fromFieldName}</fg=red> to <fg=red>{$toFieldName}</fg=red> (from <fg=red>{$this->relationsTypes[$fromField['type']]}</fg=red> to <fg=red>{$this->relationsTypes[$toField['type']]}</fg=red>).");
                        }
                    }
                }
            }
        }
    }

    /**
     * Convert Tvigle video to Sonata media.
     *
     * @param string $class
     * @param string $fromField
     * @param string $toField
     * @return array
     * @throws \RuntimeException
     */
    protected function convert($class, $fromField, $toField)
    {
        switch ($toField['type']) {
            case ClassMetadataInfo::MANY_TO_ONE: {
                return $this->convertManyToOne($class, $fromField, $toField);
                break;
            }

            case ClassMetadataInfo::MANY_TO_MANY: {
                return $this->convertManyToMany($class, $fromField, $toField);
                break;
            }

            default: {
                throw new \RuntimeException("Not supported relation type {$this->relationsTypes[$type]}.");
                break;
            }
        }
    }

    /**
     * Convert ManyToOne relation.
     *
     * @param string $class
     * @param array $fromField
     * @param array $toField
     * @return array
     */
    protected function convertManyToOne($class, $fromField, $toField)
    {
        $em    = $this->getEntityManager('default');
        $repo  = $em->getRepository($class);
        $qb    = $repo->createQueryBuilder('t');
        $count = array(
            'success' => 0,
            'errors'  => 0
        );
        
        $qb->where("t.{$fromField['fieldName']} is not null");
        
        if ($entities = $qb->getQuery()->getResult()) {
            $fromGetMethodName = 'get' .ucfirst($fromField['fieldName']);
            $fromSetMethodName = 'set' .ucfirst($fromField['fieldName']);
            $toGetMethodName   = 'get' .ucfirst($toField['fieldName']);
            $toSetMethodName   = 'set' .ucfirst($toField['fieldName']);

            foreach ($entities as $entity) {
                if ($tvigleEntity = $entity->$fromGetMethodName()) {
                    if (
                        $context     = $this->contextMap[$class] and
                        $mediaEntity = $this->createMediaFromTvigle($tvigleEntity, $context, 'sonata.media.provider.tvigle')
                    ) {
                        $entity->$fromSetMethodName(null);
                        $entity->$toSetMethodName($mediaEntity);
                        $em->persist($entity);
                        
                        $count['success'] += 1;

                    } else {
                        $count['errors'] += 1;
                    }
                }
            }

            $em->flush();
        }

        return $count;
    }

    /**
     * Convert ManyToMany relation.
     *
     * @param string $class
     * @param array $fromField
     * @param array $toField
     * @return array
     */
    protected function convertManyToMany($class, $fromField, $toField)
    {
        $em    = $this->getEntityManager('default');
        $repo  = $em->getRepository($class);
        $qb    = $repo->createQueryBuilder('t');
        $count = array(
            'success' => 0,
            'errors'  => 0
        );

        $qb->join("t.{$fromField['fieldName']}", 'tv');
        
        if ($entities = $qb->getQuery()->getResult()) {
            $fromGetMethodName = 'get' .ucfirst($fromField['fieldName']);
            $fromSetMethodName = 'set' .ucfirst($fromField['fieldName']);
            $toGetMethodName   = 'get' .ucfirst($toField['fieldName']);
            $toSetMethodName   = 'set' .ucfirst($toField['fieldName']);

            foreach ($entities as $entity) {
                if ($tvigleEntities = $entity->$fromGetMethodName()) {
                    $mediaEntities = array();

                    foreach ($tvigleEntities as $teKey=>$tvigleEntity) {
                        if (
                            $context     = $this->contextMap[$class] and
                            $mediaEntity = $this->createMediaFromTvigle($tvigleEntity, $context, 'sonata.media.provider.tvigle')
                        ) {
                            $mediaEntities[] = $mediaEntity;
                            unset($tvigleEntities[$teKey]);

                            $count['success'] += 1;

                        } else {
                            $count['errors'] += 1;
                        }
                    }

                    if ($mediaEntities) {
                        $entity->$fromSetMethodName($tvigleEntities);
                        $entity->$toSetMethodName($mediaEntities);
                        $em->persist($entity);
                    }
                }
            }

            $em->flush();
        }

        return $count;
    }

    /**
     * Create Sonata media from Tvigle video.
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $entity
     * @param string $context
     * @param string $providerName
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    protected function createMediaFromTvigle($entity, $context, $providerName)
    {
        $mediaManager = $this->getContainer()->get('sonata.media.manager.media');

        $media = new Media();
        $media->setBinaryContent($entity->getTvigleId());
        $media->setContext($context);
        $media->setProviderName($providerName);

        try {
            $mediaManager->save($media);

        } catch(\Exception $e) {
            return null;
        }

        return $media;
    }
}
