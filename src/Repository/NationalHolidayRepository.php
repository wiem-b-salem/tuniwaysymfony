<?php

namespace App\Repository;

use App\Entity\NationalHoliday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NationalHoliday>
 *
 * @method NationalHoliday|null find($id, $lockMode = null, $lockVersion = null)
 * @method NationalHoliday|null findOneBy(array $criteria, array $orderBy = null)
 * @method NationalHoliday[]    findAll()
 * @method NationalHoliday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NationalHolidayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NationalHoliday::class);
    }
}
