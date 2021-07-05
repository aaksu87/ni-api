<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\Purchased;
use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

#[AsCommand(
    name: 'csv:import',
    description: 'Import 3 csv files to related tables',
)]
class CsvImportCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * CsvImportCommand constructor.
     *
     * @param EntityManagerInterface $em
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }



    protected function configure(): void
    {
        $this->setDescription('Import 3 csv files to related tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        //********  User CSV  ***********//
        $reader = Reader::createFromPath('%kernel.root_dir%/../public/users.csv');
        $results = $reader->setHeaderOffset(0)->getRecords();
        foreach ($results as $row) {
            $user = (new User())
                ->setId($row['id'])
                ->setName($row['name'])
                ->setEmail($row['email'])
                ->setPassword($row['password']);
            $this->em->persist($user);
        }
        $this->em->flush();

        //********  Product CSV  ***********//
        $reader = Reader::createFromPath('%kernel.root_dir%/../public/products.csv');
        $results = $reader->setHeaderOffset(0)->getRecords();
        foreach ($results as $row) {
            $product = (new Product())
                ->setSku($row['sku'])
                ->setName($row['name']);
            $this->em->persist($product);
        }
        $this->em->flush();

        //********  Purchased CSV  ***********//
        $reader = Reader::createFromPath('%kernel.root_dir%/../public/purchased.csv');
        $results = $reader->setHeaderOffset(0)->getRecords();
        foreach ($results as $row) {
            $user = $this->em->getReference(User::class,$row['user_id']);
            $product = $this->em->getRepository(Product::class)->findOneBySku($row['product_sku']);

            $purchased = (new Purchased())
                ->setUser($user)
                ->setProduct($product);
            $this->em->persist($purchased);
        }
        $this->em->flush();


        $io->success('Imported');

        return Command::SUCCESS;
    }
}
