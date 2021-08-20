<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }
    /**
     * @return Query
     */

    public function findAllVisibleQuery(PropertySearch $search): Query
    {   //La fonction prend en paramettre La classe PropertySearch et
        //son type de retour est une Query
        // je vais sauvegardé le resultat de la requete de findVisibleQuery dans $query 
        $query = $this->findVisibleQuery();
        // Si j'ai un  getMaxPrice()
        if ($search->getMaxPrice()) {
            // Je rajoute à ma requête $query la clause andwhere
            $query = $query
                // Je veux que le prix de mon bien (p.price) soit inferieure ou egale
                // à maxprice (prix maxi saisi dans le formulaire)
                ->andWhere('p.price <= :maxprice')
                // La methode  setParameter , maxprice aura pour valeur =>$search->getMaxPrice()
                ->setParameter('maxprice', $search->getMaxPrice());
        }
        if ($search->getMinSurface()) {
            $query = $query
                ->andWhere('p.area >= :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }
        // Ici je retourne le resultat de la requete
        return $query->getQuery();
    }
    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.active = :active')
            ->setParameter('active', true);
    }
}
