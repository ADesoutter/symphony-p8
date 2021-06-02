<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = FakerFactory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        // on définit le nombre d'objets qu'il falloir créer
        $schoolYearCount = 10;
        $studentsPerSchoolYear = 24;
        $studentsCount = $studentsPerSchoolYear * $schoolYearCount;
        $studentsPerProject = 3;

        if ($studentsCount % $studentsPerProject == 0) {
            // valeur plancher
            $projectsCount = (int) ($studentsCount / $studentsPerProject);
        } else {
            // valeur plafond
            $projectsCount = (int) ($studentsCount / $studentsPerProject) + 1;
        }

        // on appelle les fonctions qui vont créer les objets dans la BDD
        $this->loadAdmins($manager, 3);
        $schoolYears = $this->loadSchoolYears($manager, $schoolYearCount);
        $students = $this->loadStudents($manager, $schoolYears, $studentsPerSchoolYear, $studentsCount);
        $projects = $this->loadProjects($manager, $students, $studentsPerProject, $projectsCount);
        $teachers = $this->loadTeachers($manager, $projects, 20);

        // enregistrement définitif dans la BDD
        // (envoi de la requête SQL à la BDD)
        $manager->flush();
    }

    public function loadAdmins(ObjectManager $manager, int $count)
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

        // création de users avec des données aléatoires
        // @todo préciser pourquoi $i = 1 et pas $i = 0
        for ($i = 1; $i < $count; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email());
            // hashage du mot de passe
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_ADMIN']);

            $manager->persist($user);
        }
    }

    public function loadSchoolYears(ObjectManager $manager, int $count)
    {
        // création d'un tableau qui contiendra les school years qu'on va créer
        // la fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objects puissent utiliser les school years
        $schoolYears = [];

        // création d'une school year avec des données constantes
        $schoolYear = new SchoolYear();
        $schoolYear->setName('Lorem ipsum');
        $schoolYear->setStartDate(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'));
        // récupération de la date de début
        $startDate = $schoolYear->getStartDate();
        // création de la date de fin à  partir de la date de début
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s'));
        // ajout d'un interval de 4 mois à la date de début
        $endDate->add(new \DateInterval('P4M'));
        $schoolYear->setEndDate($endDate);

        $manager->persist($schoolYear);

        // on ajoute la première school year créée
        $schoolYears[] = $schoolYear;

        // création de school years avec des données aléatoires
        for ($i = 1; $i < $count; $i++) {
            $schoolYear = new SchoolYear();
            $schoolYear->setName($this->faker->name());
            $schoolYear->setStartDate($this->faker->dateTimeThisDecade());
            // récupération de la date de début
            $startDate = $schoolYear->getStartDate();
            // création de la date de fin à  partir de la date de début
            $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s'));
            // ajout d'un interval de 4 mois à la date de début
            $endDate->add(new \DateInterval('P4M'));
            $schoolYear->setEndDate($endDate);

            $manager->persist($schoolYear);

            // on ajoute chaque school year créée
            $schoolYears[] = $schoolYear;
        }

        // on renvoit toutes les school years qui ont été créées
        return $schoolYears;
    }

    public function loadStudents(ObjectManager $manager, array $schoolYears, int $studentsPerSchoolYear, int $count)
    {
        $students = [];
        $schoolYearIndex = 0;

        $schoolYear = $schoolYears[$schoolYearIndex];

        $user = new User();
        $user->setEmail('student@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_STUDENT']);

        $manager->persist($user);

        $student = new Student();
        $student->setFirstname('Student');
        $student->setLastname('Student');
        $student->setPhone('0612345678');
        $student->setSchoolYear($schoolYear);
        $student->setUser($user);

        $manager->persist($student);
        $students[] = $student;

        for ($i = 1; $i <= $count; $i++) {
            $schoolYear = $schoolYears[$schoolYearIndex];

            if ($i % $studentsPerSchoolYear == 0) {
                $schoolYearIndex++;
            }

            $user = new User();
            $user->setEmail($this->faker->email());
            // hashage du mot de passe
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_STUDENT']);

            $manager->persist($user);

            $student = new Student();
            $student->setFirstname($this->faker->firstname());
            $student->setLastname($this->faker->lastname());
            $student->setPhone($this->faker->phoneNumber());
            $student->setSchoolYear($schoolYear);
            $student->setUser($user);

            $manager->persist($student);
            $students[] = $student;
        }

        return $students;
    }

    public function loadProjects(ObjectManager $manager, array $students, int $studentsPerProject, int $count)
    {
        $studentIndex = 0;
        $projects = [];

        // création du premier projet avec des données en dur
        $project = new Project();
        $project->setName('Hackathon');

        while (true) {
            $student = $students[$studentIndex];
            $project->addStudent($student);
            
            if (($studentIndex + 1) % $studentsPerProject == 0) {
                $studentIndex++;
                break;
            }

            $studentIndex++;
        }

        $manager->persist($project);
        $projects[] = $project;

        // création des projets suivants avec des données aléatoires
        for ($i = 1; $i < $count; $i++) {
            $project = new Project();
            $project->setName($this->faker->sentence(2));

            while (true) {
                $student = $students[$studentIndex];
                $project->addStudent($student);
        
                if (($studentIndex + 1) % $studentsPerProject == 0) {
                    $studentIndex++;
                    break;
                }

                $studentIndex++;
            }
        
            $manager->persist($project);
            $projects[] = $project;
        }

        return $projects;
    }

    public function loadTeachers(ObjectManager $manager, array $projects, int $count)
    {
        // création d'un tableau vide qui va nous permettre de stocker les teachers
        $teachers = [];

        // création du compte user avec des données constantes
        $user = new User();
        $user->setEmail('teacher@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_TEACHER']);

        $manager->persist($user);

        // création du profil teacher avec des données constantes
        $teacher = new Teacher();
        $teacher->setFirstname('Teacher');
        $teacher->setLastname('Teacher');
        $teacher->setPhone('0612345678');
        // affectation du compte user au profil qu'on vient de créer
        $teacher->setUser($user);
        // association du teacher et d'un projet constant
        // le projet constant est le premier de la liste
        $teacher->addProject($projects[0]);

        $manager->persist($teacher);

        // ajout du teacher créé au tableau
        $teachers[] = $teacher;

        // on démarre avec $i = 1 au lieu de $i = 0, car le premier
        // teacher a déjà été créé avec des données constantes
        for ($i = 1; $i < $count; $i++) {
            // création de comptes users avec des données aléatoires
            $user = new User();
            $user->setEmail($this->faker->email());
            // hashage du mot de passe
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_TEACHER']);

            $manager->persist($user);

            // création de profils teacher avec des données aléatoires
            $teacher = new Teacher();
            $teacher->setFirstname('Teacher');
            $teacher->setLastname('Teacher');
            $teacher->setPhone('0612345678');
            // affectation du compte user au profil qu'on vient de créer
            $teacher->setUser($user);

            // on détermine aléatoirement le nombre de projets associés au teacher
            $projectsCount = random_int(0, 10);
            // on créé une liste aléatoire de projets
            $randomProjects = $this->faker->randomElements($projects, $projectsCount);

            // association du teacher et des projets aléatoires
            foreach ($randomProjects as $randomProject) {
                $teacher->addProject($randomProject);
            }

            $manager->persist($teacher);

            // ajout du teacher créé au tableau
            $teachers[] = $teacher;    
        }

        return $teachers;
    }
}