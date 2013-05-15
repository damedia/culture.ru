<?php
namespace Armd\LectureBundle\Command\Migration;

use Armd\LectureBundle\Entity\LectureCategory;
use Armd\LectureBundle\Entity\LectureGenre;
use Armd\LectureBundle\Entity\LectureManager;
use Armd\LectureBundle\Entity\LectureSuperType;
use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FillGenresCommand  extends DoctrineCommand {

    protected function configure()
    {
        $this
            ->setName('armd-mk:lecture:migration-fill-genres')
            ->setDescription(
                'Fill genres based on categories'
                . ' Need this because lack of well supported ContainerAware migrations.'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearGenres();
        $this->fillCinemaGenres();
        $this->fillLectureGenres();

        $this->specifyCinemaGenres();
        $this->specifyLectureGenres();


    }

    protected function clearGenres() {
        $em = $this->getEntityManager('default');

        $em->createQuery('DELETE FROM ArmdLectureBundle:LectureGenre')->execute();

        $lectures = $em->getRepository('ArmdLectureBundle:Lecture')->findAll();
        foreach ($lectures as $lecture) {
            $lecture->setGenres(new ArrayCollection());
            $em->persist($lecture);
        }
        $em->flush();

    }

    protected function fillCinemaGenres()
    {
        $em = $this->getEntityManager('default');

        // add cinema genres lvl1

        $cinemaSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_CINEMA');

        $genres = array(
            array(
                'title' => 'Художественные фильмы',
                'slug' => 'feature-film',
            ),
            array(
                'title' => 'Документальные фильмы',
                'slug' => 'documentary',
            ),
            array(
                'title' => 'Детские и мультфильмы',
                'slug' => 'child',
            ),
            array(
                'title' => '100 фильмов для школьников',
                'slug' => 'child-100',
            ),
            array(
                'title' => '100 зарубежных фильмов',
                'slug' => 'foreign-100',
            ),
        );

        foreach ($genres as $genreData) {
            $genre = new LectureGenre();
            $genre->setTitle($genreData['title']);
            $genre->setSlug($genreData['slug']);
            $genre->setLectureSuperType($cinemaSuperType);
            $genre->setLevel(1);

            $em->persist($genre);
        }

        $em->flush();

        // add cinema genres lvl2
        $rootCategory = $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->findOneBy(array('lectureSuperType' => $cinemaSuperType));

        $categories = $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->createQueryBuilder('c')
            ->where('c.lft > :lft')
            ->andWhere('c.rgt < :rgt')
            ->andWhere('c.lvl = 2')
            ->setParameters(
                array(
                    'lft' => $rootCategory->getLft(),
                    'rgt' => $rootCategory->getRgt()
                )
            )->getQuery()->getResult();

        foreach ($categories as $category) {
            $genre = new LectureGenre();
            $genre->setTitle($category->getTitle());
            $genre->setLectureSuperType($cinemaSuperType);
            $genre->setLevel(2);

            $em->persist($genre);
        }

        $em->flush();
    }

    protected function fillLectureGenres()
    {
        $em = $this->getEntityManager('default');

        $lectureSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_LECTURE');

        $genresData = array('Мастер-классы', 'Гуманитарные', 'Технические', 'Естественные науки');
        foreach ($genresData as $genreData) {
            $genre = new LectureGenre();
            $genre->setTitle($genreData);
            $genre->setLevel(1);
            $genre->setLectureSuperType($lectureSuperType);
            $em->persist($genre);
        }

        $em->flush();

//        $rootCategory = $em->getRepository('ArmdLectureBundle:LectureCategory')
//            ->findOneBy(array('lectureSuperType' => $lectureSuperType));
//
//        $categories = $em->getRepository('ArmdLectureBundle:LectureCategory')
//            ->createQueryBuilder('c')
//            ->where('c.lft > :lft')
//            ->andWhere('c.rgt < :rgt')
//            ->setParameters(
//                array(
//                    'lft' => $rootCategory->getLft(),
//                    'rgt' => $rootCategory->getRgt()
//                )
//            )->getQuery()->getResult();
//
//        foreach ($categories as $category) {
//            $genre = new LectureGenre();
//            $genre->setTitle($category->getTitle());
//            $genre->setLectureSuperType($lectureSuperType);
//            $genre->setLevel(1);
//
//            $em->persist($genre);
//        }
//
//        $em->flush();


    }

    protected function specifyCinemaGenres() {
        $em = $this->getEntityManager('default');

        // set cinema 1 level genre
        $qb = $em->getRepository('ArmdLectureBundle:Lecture')
            ->createQueryBuilder('l');

        $qb->innerJoin('l.categories', 'c')
            ->innerJoin('l.lectureSuperType', 'st')
            ->leftJoin('c.parent', 'cp')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('cp.title', ':category_name'),
                $qb->expr()->eq('c.title', ':category_name')
            ))
            ->andWhere('c.title NOT IN (:not_cinema_category_names)')
            ->andWhere("st.code = 'LECTURE_SUPER_TYPE_CINEMA'")
            ->setParameter('category_name', 'Кинофильмы')
            ->setParameter('not_cinema_category_names', array('Мультфильмы', 'Документальное', 'Сказка', 'Фильм-сказка'));

        $cinemas = $qb->getQuery()->getResult();
        echo 'cinema ' . count($cinemas);

        $genre = $em->getRepository('ArmdLectureBundle:LectureGenre')
            ->findOneByTitle('Художественные фильмы');

        foreach($cinemas as $cinema) {
            $cinema->addGenre($genre);
            $em->persist($cinema);
        }

        // set cartoons 1 level genre
        $qb = $em->getRepository('ArmdLectureBundle:Lecture')
            ->createQueryBuilder('l');

        $qb->innerJoin('l.categories', 'c')
            ->innerJoin('l.lectureSuperType', 'st')
            ->innerJoin('c.parent', 'cp')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('cp.title', ':category_name'),
                $qb->expr()->eq('c.title', ':category_name')
            ))
            ->andWhere("st.code = 'LECTURE_SUPER_TYPE_CINEMA'")
            ->setParameter('category_name', 'Мультфильмы');

        $cinemas = $qb->getQuery()->getResult();
        echo 'cartoon ' . count($cinemas);

        $genre = $em->getRepository('ArmdLectureBundle:LectureGenre')
            ->findOneByTitle('Детские и мультфильмы');

        foreach($cinemas as $cinema) {
            $cinema->addGenre($genre);
            $em->persist($cinema);
        }

        // set documentary 1 level genre
        $qb = $em->getRepository('ArmdLectureBundle:Lecture')
            ->createQueryBuilder('l');

        $qb->innerJoin('l.categories', 'c')
            ->innerJoin('l.lectureSuperType', 'st')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('c.title', ':category_name')
            ))
            ->andWhere("st.code = 'LECTURE_SUPER_TYPE_CINEMA'")
            ->setParameter('category_name', 'Документальное');

        $cinemas = $qb->getQuery()->getResult();
        echo 'cartoon ' . count($cinemas);

        $genre = $em->getRepository('ArmdLectureBundle:LectureGenre')
            ->findOneByTitle('Документальные фильмы');

        foreach($cinemas as $cinema) {
            $cinema->addGenre($genre);
            $em->persist($cinema);
        }

        // set top 100 1 level genre
        $qb = $em->getRepository('ArmdLectureBundle:Lecture')
            ->createQueryBuilder('l')
            ->innerJoin('l.categories', 'c')
            ->innerJoin('l.lectureSuperType', 'st')
            ->innerJoin('c.parent', 'cp')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('cp.title', ':category_name'),
                $qb->expr()->eq('c.title', ':category_name'),
                $qb->expr()->eq('l.isTop100Film', 'true')
            ))
            ->andWhere("st.code = 'LECTURE_SUPER_TYPE_CINEMA'")
            ->setParameter('category_name', '100 фильмов для школьников');

        $cinemas = $qb->getQuery()->getResult();
        echo 'school ' . count($cinemas);

        $genre = $em->getRepository('ArmdLectureBundle:LectureGenre')
            ->findOneByTitle('100 фильмов для школьников');

        foreach($cinemas as $cinema) {
            $cinema->addGenre($genre);
            $em->persist($cinema);
        }

        // set cinema 2 lvl genres
        $cinemas = $em->getRepository('ArmdLectureBundle:Lecture')
            ->createQueryBuilder('l')
            ->innerJoin('l.categories', 'c')
            ->innerJoin('l.lectureSuperType', 'st')
            ->andWhere("st.code = 'LECTURE_SUPER_TYPE_CINEMA'")
            ->andWhere('c.lvl = 2')
            ->getQuery()->getResult();

        foreach ($cinemas as $cinema) {
            foreach($cinema->getCategories() as $category) {
                $genre = $em->getRepository('ArmdLectureBundle:LectureGenre')
                    ->createQueryBuilder('g')
                    ->innerJoin('g.lectureSuperType', 'st')
                    ->where("st.code = 'LECTURE_SUPER_TYPE_CINEMA'")
                    ->andWhere('g.level = 2')
                    ->andWhere('g.title = :title')->setParameter('title', $category->getTitle())
                    ->getQuery()->getOneOrNullResult();

                if ($genre) {
                    if ($genre->getTitle() === '100 лучших зарубежных фильмов') {
                        $genre = $em->getRepository('ArmdLectureBundle:LectureGenre')
                            ->findOneByTitle('100 зарубежных фильмов');
                        $cinema->addGenre($genre);

                    } elseif  ($genre->getTitle() === 'Документальное') {
                        $genre = $em->getRepository('ArmdLectureBundle:LectureGenre')
                            ->findOneByTitle('Документальные фильмы');
                        $cinema->addGenre($genre);

                    } elseif ($genre->getTitle() === 'Сказка' || $genre->getTitle() === 'Фильма-сказка') {
                        $cinema->addGenre($genre);
                        $genre = $em->getRepository('ArmdLectureBundle:LectureGenre')
                            ->findOneByTitle('Детские и мультфильмы');
                        $cinema->addGenre($genre);

                    } else {
                        $cinema->addGenre($genre);
                    }
                    $em->persist($cinema);
                }
            }
        }

        $em->flush();
    }

    protected function specifyLectureGenres() {
        $em = $this->getEntityManager('default');

        $lectures = $em->getRepository('ArmdLectureBundle:Lecture')
            ->createQueryBuilder('l')
            ->innerJoin('l.categories', 'c')
            ->innerJoin('l.lectureSuperType', 'st')
            ->where("st.code = 'LECTURE_SUPER_TYPE_LECTURE'")
            ->getQuery()->getResult()
        ;

        $genres = $em->createQuery("
            SELECT g
                FROM ArmdLectureBundle:LectureGenre g INDEX BY g.title
                INNER JOIN g.lectureSuperType st
                WHERE st.code = 'LECTURE_SUPER_TYPE_LECTURE'
        ")->getResult();

        $conversions = array(
            'Гуманитарные' => array(
                'Востоковедение',
                'Зарубежная история',
                'Зарубежная литература',
                'Лингвистика',
                'Искусство',
                'История России',
                'Психология',
                'Философия',
                'ЖЗЛ',
                'Религия',
                'Русская литература',
                'Русский язык',
                'Мифология',
                'Музыка',
                'Человек',
                'Экономика'
            ),
            'Технические' => array(
                'Современные технологии'
            ),
            'Естественные науки' => array(
                'Биология',
                'Генетика',
                'География',
                'Планета Земля',
                'Глобальные проблемы',
                'Астрономия',
                'Математика',
                'Физика',
                'Химия'
            )
        );

        foreach ($lectures as $lecture) {
            if (mb_stripos($lecture->getTitle(), 'Мастер-класс', null, 'utf-8') !== false) {
                $lecture->addGenre($genres['Мастер-классы']);
            }

            foreach ($lecture->getCategories() as $category) {
                foreach ($conversions as $genreTitle => $categoryTitles) {
                    foreach ($categoryTitles as $categoryTitle) {
                        if ($category->getTitle() === $categoryTitle) {
                            $lecture->addGenre($genres[$genreTitle]);
                        }
                    }
                }
           }
            $em->persist($lecture);
        }

        $em->flush();
    }

}