<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProdFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getGroups(): array
    {
        // Cette fixture fait partie du groupe "prod".<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findAllAdmins()
    {
        return $this->findByRole('ROLE_ADMIN');
    }

    /**
     * @param $role string nom d'un rôle comme 'ROLE_ADMIN', 'ROLE_STUDENT', etc
     * @return User[] Returns an array of User objects
     */
    public function findByRole(string $role)
    {
        // cette requête génère le code DQL suivant :
        // "SELECT u FROM App\Entity\User u WHERE u.roles LIKE :role ORDER BY u.email ASC"
        // 'u' sera l'alias qui permet de désigner un user
        return $this->createQueryBuilder('u')
            // ajout d'un filtre qui ne retient que les users
            // qui contiennent (opérateur LIKE) la chaîne de
            // caractères contenue dans la variable :role
            ->andWhere('u.roles LIKE :role')
            // affactation d'une valeur à la variable :role
            // le symbole % est joker qui veut dire
            // « match toutes les chaînes de caractères »
            ->setParameter('role', "%{$role}%")
            // tri par adresse email en ordre croissant (a, b, c, ...)
            ->orderBy('u.email', 'ASC')
            // récupération d'une requête qui n'attend qu'à être exécutée
            ->getQuery()
            // exécution de la requête
            // récupération d'un tableau de résultat
            // ce tableau peut contenir, zéro, un ou plusieurs lignes
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
        // Cela permet de cibler seulement certains fixtures
        // quand on exécute la commande doctrine:fixtures:load.
        // Pour que la méthode statique getGroups() soit prise
        // en compte, il faut que la classe implémente
        // l'interface FixtureGroupInterface.
        return ['prod'];
    }

    public function load(ObjectManager $manager)
    {
       
        $this->loadAdmin($manager);

        $manager->flush();
    }
    public function loadAdmin(ObjectManager $manager)
    {
        // création d'un user avec des données constantes
        // ici il s'agit du compte admin
        $user = new User();
        $user->setEmail('admin@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
    }
}