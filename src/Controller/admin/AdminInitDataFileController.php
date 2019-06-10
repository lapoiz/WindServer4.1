<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 13/04/2019
 * Time: 23:43
 */

namespace App\Controller\admin;


use App\Entity\Region;
use App\Utils\GetDataMaree;
use App\Entity\InitDataFile;
use App\Entity\Spot;
use App\Form\InitDataFileType;
use App\Utils\GetDataWindOrientation;
use App\Utils\WebsiteGetData;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminInitDataFileController extends AbstractController
{
    var $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/data_file/generate", name="admin_generate_data_file")
     * @param Request $request
     * @return
     */
    public function generateDataFileAction(Request $request)
    {
        try {
            $filesystem = new Filesystem();
            $fileName = $this->getDownloadFileName();
            $filesystem->touch($fileName);
            $this->addFlash('success', 'Fichier généré');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'probleme:'.$exception->getMessage());
        }
        return $this->initDataFileAction($request);
    }

    /**
     * @Route("/data_file/download", name="admin_download_data_file")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDataFileAction(Request $request)
    {
        $filename = $this->getDownloadFileName();
        return $this->file($filename);

    }


    /**
     * @Route("/data_file/init", name="admin_init_data_file")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function initDataFileAction(Request $request)
    {
        $initDataFile = new InitDataFile();
        $form = $this->createForm(InitDataFileType::class, $initDataFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded CSV file
            /** @var UploadedFile $file */
            $file = $initDataFile->getDataFile();

            $fileName = $this->generateInitDataFileName();
            $directoryName= $this->getParameter('init_data_directory');

            // Move the file to the directory
            try {
                $this->import($file);
                $file->move(
                    $directoryName,
                    $fileName
                );
                $this->addFlash('success', 'Fichier uploadé');
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                $this->addFlash('danger', 'Erreur'.$e.getMessage());
            }

            $initDataFile->setDataFile($fileName);
            return $this->redirectToRoute("admin_spot_index");
        }

        return $this->render('admin/dataFile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param UploadedFile $file
     * Import le fichier CVS mis en parametre
     *
     */
    private function import(UploadedFile $file)
    {
        $data = $this->csv_to_array($file->getPathname());
        if ($data != null) {
            // Turning off doctrine default logs queries for saving memory
            $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);

            $size = count($data);
            $batchSize = 5; // tout les X elements on enregistre
            $i = 1;

            // Starting progress

            // Récupére les régions existantes (doivent être créé avant)
            $tabRegions = $this->getTabRegions();

            // Processing on each row of data
            foreach ($data as $row) {
                try {
                    $spot = $this->importRow($row,$tabRegions);

                    //$this->manager->merge($spot);

                    if (($i % $batchSize) === 0) {
                        $this->manager->flush();
                        // Detaches all objects from Doctrine for memory save
                        $this->manager->clear();
                    }
                    $i++;
                    // Advancing for progress display on console

                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur'.$e->getMessage());
                }
                $i++;
            }
        }
        $this->manager->flush();
    }

    /**
     * Convert a comma separated file into an associated array.
     * The first row should contain the array keys.
     *
     * Example:
     *
     * @param string $filename Path to the CSV file
     * @param string $delimiter The separator used in the file
     * @return array
     * @link http://gist.github.com/385876
     * @author Jay Williams <http://myd3.com/>
     * @copyright Copyright (c) 2010, Jay Williams
     * @license http://www.opensource.org/licenses/mit-license.php MIT License
     */
    private function csv_to_array($filename='', $delimiter=';')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }

    private function importRow($row,$tabRegions)
    {
        //Todo
        $spot = new Spot();
        $spot->setName($row['Nom']);

        if (!empty($row['Description'])) {
            $spot->setDescription($this->cleanDescription($row['Description']));
        }

        // ---------- Route --------------
        if (!empty($row['Localisation'])) {
            $spot->setDescRoute($this->cleanDescription($row['Localisation']));
        }
        if (!empty($row['DistFromParis'])  and trim($row['DistFromParis']) != '') {
            $spot->setKmFromParis($row['DistFromParis']);
        }
        if (!empty($row['DistFromParisAutoroute']) and trim($row['DistFromParisAutoroute']) != '') {
            $spot->setKmAutorouteFromParis($row['DistFromParisAutoroute']);
        }
        if (!empty($row['TimeFromParis']) and trim($row['TimeFromParis']) != '') {
            $spot->setTimeFromParis($row['TimeFromParis']);
        }
        if (!empty($row['PéageFromParis']) and trim($row['PéageFromParis']) != '') {
            $spot->setPriceAutorouteFromParis($row['PéageFromParis']);
        }

        if (!empty($row['MareeDesc'])) {
            $spot->setDescMaree($this->cleanDescription($row['MareeDesc']));
        }
        if (!empty($row['OrientationDesc'])) {
            $spot->setDescOrientationVent($this->cleanDescription($row['OrientationDesc']));
        }

        $spot->setGpsLong($row['Long']);
        $spot->setGpsLat($row['Lat']);

        if (!empty($row['Windfinder']) and trim($row['Windfinder']) != '') {
            $spot->setUrlWindFinder($row['Windfinder']);
        }
        if (!empty($row['Windguru']) and trim($row['Windguru']) != '') {
            $spot->setURLWindguru($row['Windguru']);
        }
        if (!empty($row['Meteo France']) and trim($row['Meteo France']) != '') {
            $spot->setURLMeteoFrance($row['Meteo France']);
        }
        if (!empty($row['MeteoConsult']) and trim($row['MeteoConsult']) != '') {
            $spot->setURLMeteoConsult($row['MeteoConsult']);
        }
        if (!empty($row['Merteo']) and trim($row['Merteo']) != '') {
            $spot->setURLMerteo($row['Merteo']);
        }
        if (!empty($row['Temp eau']) and trim($row['Temp eau']) != '') {
            $spot->setURLTempWater($row['Temp eau']);
        }
        if (!empty($row['Maree']) and trim($row['Maree']) != '') {
            GetDataMaree::putDataFromMareeInfo($spot,$row['Maree']);

            $marreRestrictionArray = array('OK','warn','KO');
            foreach ($marreRestrictionArray as $mareeState) {
                if (!empty($row[$mareeState])) {
                    GetDataMaree::putMareeRestriction($spot,$row[$mareeState], $mareeState);
                }
            }
        }

        /* @var \App\Entity\WindOrientation $orientation */
        foreach ($spot->getWindOrientation() as $orientation) {
            $orientation->setState(WebsiteGetData::transformeOrientationState($row[$orientation->getOrientation()]));
        }
        //GetDataWindOrientation::getDataFromDataCSV($row,$spot);

        //Todo: UrlWebcam
        //Todo: UrlTemWater
        //Todo: UrlBalise

        //if (!empty($row['Region']) && !empty($tabRegions[$row['Region']])) {
            /** @var Region $region  */
            /*$region = $tabRegions[$row['Region']];
            $spot->setRegion($region);
            $region->addSpot($spot);

            $this->manager->persist($spot);
            $this->manager->flush($region);
        } else {
            */
            $this->manager->persist($spot);

        //}

        return $spot;
    }

    private function cleanDescription($desc)
    {
        // Enléve les <br /> pour mettre à la ligne
        $desc = str_replace('<br/>', '\u239D', $desc);
        $desc = str_replace('<br />', '\u240D', $desc);
        $desc = str_replace('<b>', '\u241D', $desc);
        $desc = str_replace('</b>', '\u242D', $desc);
        $desc = str_replace('<ul>', '\u243D', $desc);
        $desc = str_replace('</ul>', '\u244D', $desc);
        $desc = str_replace('<li>', '\u245D', $desc);
        $desc = str_replace('</li>', '\u246D', $desc);
        $desc=htmlentities($desc);
        $desc = str_replace('\u239D', '<br />' , $desc);
        $desc = str_replace('\u240D', '<br />' , $desc);
        $desc = str_replace('\u241D', '<b>',  $desc);
        $desc = str_replace('\u242D', '</b>', $desc);
        $desc = str_replace('\u243D', '<ul>', $desc);
        $desc = str_replace('\u244D', '</ul>', $desc);
        $desc = str_replace('\u245D', '<li>', $desc);
        $desc = str_replace('\u246D', '</li>', $desc);
        return $desc;
    }

    private function getDownloadFileName()
    {
        $filename = $this->getParameter('download_data_directory');
        return $filename.'/data.csv';

    }

    private function generateInitDataFileName()
    {
        return 'InitDataFile'.uniqid().'.csv';
    }


    private function getTabRegions() {
        $result = array();
        $regionList = $this->manager->getRepository(Region::class)->findAll();

        foreach ($regionList as $region) {
            $result[$region->getNom()] = $region;
        }
        return $result;
    }
}