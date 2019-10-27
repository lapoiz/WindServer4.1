<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 27/10/2019
 * Time: 13:29
 */

namespace App\Command;


use App\Entity\Spot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;

class GenerateCardCommand extends Command
{

    protected static $defaultName = 'app:generate-card';

    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var RouterInterface */
    private $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->entityManager = $em;
        $this->router = $router;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Genere les Cards pour chaque site.')
            ->setHelp('Cette commande permet de générer les card au format png.')
            ->addArgument('spotId', InputArgument::OPTIONAL, 'Id du spot à générer.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $spots = $this->entityManager->getRepository(Spot::class)->findAll();

        /**
         * @var Spot $spot
         */
        foreach ($spots as $spot) {
            $output->writeln("--------------------------------------- ");
            $output->writeln("spot : ".$spot->getName());
            $url = $this->router->generate('admin.spot.show.card', array('id' => $spot->getId()));
            $command = "wkhtmltoimage --format 'jpg' '".$url."' '/var/www/wind/public/cards/card.".$spot->getId().".jpg'";
            $output->writeln("commande: ".$command);
            $process = new Process($command);
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                $output->writeln("Error : ".$process->getErrorOutput());
            } else {
                $output->writeln($process->getOutput());
            }
        }
    }
}