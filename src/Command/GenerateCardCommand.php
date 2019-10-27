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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;

class GenerateCardCommand extends Command
{

    protected static $defaultName = 'app:generate-card';

    protected $cmdOptions = "--width 300 --height 500";// --format 'jpg'";

    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var RouterInterface */
    private $router;
    /** @var ParameterBagInterface */
    private $params;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, ParameterBagInterface $params)
    {
        $this->entityManager = $em;
        $this->router = $router;
        $this->params = $params;

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
        $os=$_ENV['OS_NAME'];

        $commandExec = "wkhtmltoimage";
        $pathCard="/var/www/wind/public/cards/";
        if ('windows'===$os) {
            $commandExec = "wkhtmltoimage.exe";
            $pathCard=str_replace('/',DIRECTORY_SEPARATOR,$this->params->get('card_directory_kernel')).DIRECTORY_SEPARATOR; //.$this->params->get('kernel.project_dir').DIRECTORY_SEPARATOR;
        }

        $spotId = $input->getArgument('spotId');
        if ($spotId) {
            $this->commandeGenerateCard($output,$spotId,$commandExec,$pathCard, null);
        } else {
            $spots = $this->entityManager->getRepository(Spot::class)->findAll();

            /**
             * @var Spot $spot
             */
            foreach ($spots as $spot) {
                $this->commandeGenerateCard($output,$spot->getId(),$commandExec,$pathCard,$spot->getName());
            }
        }
    }

    private function commandeGenerateCard(OutputInterface $output, String $spotId, String $commandExec, String $pathCard, $spotName)
    {
        $output->writeln("--------------------------------------- ");
        if ($spotName) {
            $output->writeln("spot : " . $spotName);
        } else {
            $output->writeln("spot ID : " . $spotId);
        }
        $url = $this->router->generate('admin.spot.show.card', array('id' => $spotId), UrlGenerator::ABSOLUTE_URL);
        $command = $commandExec." ".$this->cmdOptions." '" . $url . "' ".$pathCard."card." . $spotId . ".jpg";
        $output->writeln("commande: " . $command);
        $process = new Process($command);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            $output->writeln("Error : " . $process->getErrorOutput());
        } else {
            $output->writeln($process->getOutput());
        }
    }
}