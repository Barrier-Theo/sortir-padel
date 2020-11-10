<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Inscription;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    // SORTIES FILTERS
    public function searchSorties(SearchData $search, $userId)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->leftJoin('s.Campus', 'campus')
            ->leftJoin('s.etat', 'etat')
            ->leftJoin('s.inscriptions', 'inscriptions')
            ->leftJoin('inscriptions.participant', 'participant');

        // Campus option
        if (!empty($search->campus)) {
            $query = $query
                ->andWhere('campus.nom = :campusName')
                ->setParameter('campusName', $search->campus->getNom());
        }

        // Search bar
        if (!empty($search->q)) {
            $query = $query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        // Start date
        if (!empty($search->dateDebut)) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $search->dateDebut);
        }

        // End date
        if (!empty($search->dateFin)) {
            $query = $query
                ->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $search->dateFin);
        }

        // Is organisateur
        if (!empty($search->estOrganisateur)) {
            $query = $query
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $userId);
        }

        // Is inscrit
        if (!empty($search->estInscrit)) {
            $query = $query
                ->andWhere('participant.id = :participantId')
                ->setParameter('participantId', $userId);
        }

        $subqb = $this->createQueryBuilder('s2')->select('s2.id');
        $subqb->leftJoin('s2.inscriptions', 'i2');
        $subqb->andWhere('i2.participant = :me');

        // Isn't inscrit
        if (!empty($search->pasInscrit)) {
            $query = $query
            ->andWhere($query->expr()->notIn('s.id', $subqb->getDQL()))
            ->setParameter('me', $userId);
        }

        // Passed
        if (!empty($search->sortiePassee)) {
            $query = $query
                ->andWhere('etat.libelle = :etatLibelle')
                ->setParameter('etatLibelle', "passee");
        }

        return $query->getQuery()->getResult();
    }
}
