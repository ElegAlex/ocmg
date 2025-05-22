<?php
/**
 * Created by PhpStorm.
 * User: delepine
 * Date: 18/09/18
 * Time: 15:03
 */

namespace App\Repository;

use CNAMTS\PHPK\CoreBundle\Data\Repository;
use Doctrine\ORM\EntityRepository;


/**
 * CiblageRepository
 *
 * @package App\Repository
 */
class CiblageRepository extends EntityRepository implements Repository
{
    /**
     * Methode du repository qui recupère la liste des ciblages de la table ciblage
     * la méthode liste est utilisée pour l'enrichissement en data de Tableaux/ListeCiblageName.php
     *
     * @return array|int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function liste()
    {

        $connexion = $this->getEntityManager()->getConnection();

        $sql = 'SELECT 
                        c.id as id   ,                 
                      c.ciblageName AS ciblageName
                                
                    FROM ciblage c';

        $statement = $connexion->query($sql);
        return $statement->fetchAll();

    }


}