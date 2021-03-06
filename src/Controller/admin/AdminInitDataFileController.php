<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 13/04/2019
 * Time: 23:43
 */

namespace App\Controller\admin;


use App\Entity\MareeRestriction;
use App\Entity\Region;
use App\Entity\WebSiteInfo;
use App\Entity\WindOrientation;
use App\Repository\SpotRepository;
use App\Service\DisplayObject;
use App\Service\HTMLtoImage;
use App\Service\MareeToImage;
use App\Utils\GetDataMaree;
use App\Entity\InitDataFile;
use App\Entity\Spot;
use App\Form\InitDataFileType;
use App\Service\RosaceWindManage;
use App\Utils\WebsiteGetData;
use Doctrine\Common\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminInitDataFileController extends AbstractController
{
    /**
    * @var SpotRepository
    */
    private $repository;
    var $manager;

    public function __construct(SpotRepository $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }


    /**
     * @Route("/data_file/generate", name="admin_generate_data_file")
     * @param HTMLtoImage $imageGenerator
     * @param MareeToImage $mareetoImage
     * @param RosaceWindManage $rosaceWindManage
     * @param DisplayObject $displayObject
     * @param Request $request
     * @return
     */
    public function generateDataFileAction(Request $request, HTMLtoImage $imageGenerator, MareeToImage $mareetoImage,
                                           RosaceWindManage $rosaceWindManage, DisplayObject $displayObject)
    {
        try {
            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Spots from LaPoiz");

            $allSpots = $this->repository->findAll();
            $this->exportInExcel($sheet, $allSpots);

            // Create your Office 2007 Excel (XLSX Format)
            $writerXlsx = new Xlsx($spreadsheet);

            $fileName = $this->getDownloadFileName();
            // Create the file
            $writerXlsx->save($fileName.".xlsx");

            $this->addFlash('success', 'Fichier généré');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'probleme:'.$exception->getMessage());
        }
        return $this->initDataFileAction($request, $imageGenerator, $mareetoImage, $rosaceWindManage, $displayObject);
    }

    /**
     * @Route("/data_file/download", name="admin_download_data_file")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDataFileAction(Request $request)
    {
        $filename = $this->getDownloadFileName();
        return $this->file($filename.".xlsx");
    }


    /**
     * @Route("/data_file/init", name="admin_init_data_file")
     * @param Request $request
     * @param HTMLtoImage $imageGenerator
     * @param MareeToImage $mareetoImage
     * @param RosaceWindManage $rosaceWindManage
     * @param DisplayObject $displayObject
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function initDataFileAction(Request $request, HTMLtoImage $imageGenerator, MareeToImage $mareetoImage, RosaceWindManage $rosaceWindManage, DisplayObject $displayObject)
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
                $this->import($file,$imageGenerator, $mareetoImage, $rosaceWindManage);
                $file->move(
                    $directoryName,
                    $fileName
                );
                $this->addFlash('success', 'Fichier uploadé');

                $initDataFile->setDataFile($fileName);
                return $this->redirectToRoute("admin.spots.maree.to.image");
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                $this->addFlash('danger', 'Erreur'.$e.getMessage());
                $initDataFile->setDataFile($fileName);
                return $this->redirectToRoute("admin_init_data_file");
            }

        }
        $spots = $this->repository->findAll();
        return $this->render('admin/dataFile/index.html.twig', [
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param UploadedFile $file
     * Import le fichier XLSX mis en parametre
     */
    private function import(UploadedFile $file,HTMLtoImage $imageGenerator, MareeToImage $mareetoImage, RosaceWindManage $rosaceWindManage)
    {
        $spreadsheet = $this->excel_to_spreadsheet($file->getPathname());
        if ($spreadsheet != null) {
            $data=$this->spreadsheet_to_data($spreadsheet);

            // Turning off doctrine default logs queries for saving memory
            $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);

            $data = $data["Spots from LaPoiz"]; // Nom de l'onglet
            //$nbCol = count($data["columnValues"]);
            $batchSize = 5; // tout les X lignes on enregistre
            $i = 1;

            // Récupére les régions existantes (doivent être créé avant)
            $tabRegions = $this->getTabRegions();

            // Processing on each row of data
            foreach ($data["columnValues"] as $row) {
                try {
                    $spot = $this->importRow($row,$tabRegions, $imageGenerator, $mareetoImage, $rosaceWindManage);

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
        } else {
            $this->addFlash('danger', 'Erreur impossible de charger le fichier.');
        }
        $this->manager->flush();
    }

    private function excel_to_spreadsheet($filename='')
    {
        if(file_exists($filename) && is_readable($filename))
        {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            return $reader->load($filename);
        } else {
            return null;
        }
    }

    protected function spreadsheet_to_data($spreadsheet)
    {
        $data = [];
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $data[$worksheetTitle] = [
                'columnNames' => [],
                'columnValues' => [],
            ];
            foreach ($worksheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                $colIndex = 0;
                if ($rowIndex > 2) {
                    $data[$worksheetTitle]['columnValues'][$rowIndex] = [];
                }
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop over all cells, even if it is not set
                foreach ($cellIterator as $cell) {
                    // boucle sur les valeur de la ligne
                    if ($rowIndex === 1) {
                        // premiere ligne => ligne des titres de col
                        $data[$worksheetTitle]['columnNames'][] = $cell->getCalculatedValue();
                    }
                    if ($rowIndex > 1) {
                        // ligne de valeur
                        $data[$worksheetTitle]['columnValues'][$rowIndex][$data[$worksheetTitle]['columnNames'][$colIndex]] = $cell->getCalculatedValue();
                        $colIndex++;
                    }
                }
            }
        }

        return $data;
    }

    private function importRow($row,$tabRegions, HTMLtoImage $hTMLtoImage, MareeToImage $mareetoImage, RosaceWindManage $rosaceWindManage)
    {
        $spot = new Spot();
        $spot->setName($row['Nom']);

        if (!empty($row['CodeSpot'])) {
            $spot->setCodeSpot($row['CodeSpot']);
        }

        if (!empty($row['Note'])) {
            $spot->setNoteGeneral($this->cleanDecimal($row['Note']));
        }

        if (!empty($row['ShortDescription'])) {
            $spot->setShortDescription($this->cleanDescription($row['ShortDescription']));
        }

        if (!empty($row['Description'])) {
            $spot->setDescription($this->cleanDescription($row['Description']));
        }

        if (!empty($row['WaveDesc'])) {
            $spot->setDescWave($this->cleanDescription($row['WaveDesc']));
        }

        if (!empty($row['IsFoil'])) {
            $spot->setIsContraintEte($this->isIt($row['IsFoil']));
        }

        if (!empty($row['FoilDesc'])) {
            $spot->setDescFoil($this->cleanDescription($row['FoilDesc']));
        }

        if (!empty($row['ContraintEteDesc'])) {
            $spot->setDescContraintEte($this->cleanDescription($row['ContraintEteDesc']));
        }

        if (!empty($row['IsContraintEte'])) {
            $spot->setIsContraintEte($this->isIt($row['IsContraintEte']));
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
            $spot->setPriceAutorouteFromParis($this->cleanDecimal($row['PéageFromParis']));
        }

        if (!empty($row['MareeDesc'])) {
            $spot->setDescMaree($this->cleanDescription($row['MareeDesc']));
        }
        if (!empty($row['OrientationDesc'])) {
            $spot->setDescOrientationVent($this->cleanDescription($row['OrientationDesc']));
        }

        $spot->setGpsLong($this->cleanDecimal($row['Long']));
        $spot->setGpsLat($this->cleanDecimal($row['Lat']));

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

            $marreRestrictionArray = array('top','OK','warn','KO');
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


        if (!empty($row['Webcam']) and trim($row['Webcam']) != '') {
            $spot->setUrlWebcam($row['Webcam']);
        }
        if (!empty($row['Balise']) and trim($row['Balise']) != '') {
            $spot->setUrlBalise($row['Balise']);
        }
        if (!empty($row['Temp eau']) and trim($row['Temp eau']) != '') {
            $spot->setURLTempWater($row['Temp eau']);
        }

        $this->getWebsiteInfo($row, $spot, '1');
        $this->getWebsiteInfo($row, $spot, '2');
        $this->getWebsiteInfo($row, $spot, '3');
        $this->getWebsiteInfo($row, $spot, '4');


        if (!empty($row['CodeRegion'])) {
            $repositoryRegion = $this->getDoctrine()->getRepository(Region::class);
            /** @var Region $region  */
            $region = $repositoryRegion->findOneByCodeRegion($row['CodeRegion']);
            if ($region != null) {
                $spot->setRegion($region);
                $region->addSpot($spot);
                $this->manager->persist($spot);
                $this->manager->flush($region);
            } else {
                $this->manager->persist($spot);
            }
        } else {
            $this->manager->persist($spot);
        }
        $urlImage=$this->getParameter('rosace_directory_kernel');
        $rosaceWindManage->createRosaceWind($spot,$urlImage);
        $mareetoImage->createImageMareeFromSpot($spot);
        $hTMLtoImage->createImageCard($spot);

        return $spot;
    }

    private function cleanDescription($desc)
    {
        // Enléve les <br /> pour mettre à la ligne
        /*
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
        */
        return $desc;
    }

    private function cleanDecimal($number) {
        return floatval($number);
    }

    private function isIt($cellData) {
        if ($cellData=="1") {
            return true;
        } else {
            return false;
        }
    }

    private function getDownloadFileName()
    {
        $filename = $this->getParameter('download_data_directory');
        return $filename.'/data';

    }

    private function generateInitDataFileName()
    {
        return 'InitDataFile'.uniqid().'.xlsx';
    }

    /**
     * @param $row
     * @param Spot $spot
     * @param $num
     */
    private function getWebsiteInfo($row, $spot, $num) {
        $websiteInfo=null;
        if (!empty($row['URL'.$num]) and trim($row['URL'.$num]) != '') {
            $websiteInfo = new WebSiteInfo();
            $websiteInfo->setUrl($row['URL'.$num]);
        }
        if ($websiteInfo!=null) {
            if (!empty($row['Titre'.$num]) and trim($row['Titre'.$num]) != '') {
                $websiteInfo->setName($row['Titre'.$num]);
            }
            if (!empty($row['Commentaire'.$num]) and trim($row['Commentaire'.$num]) != '') {
                $websiteInfo->setDescription($row['Commentaire'.$num]);
            }
            $websiteInfo->setSpot($spot);
            $spot->addWebSiteInfos($websiteInfo);
        }
    }

    private function getTabRegions() {
        $result = array();
        $regionList = $this->manager->getRepository(Region::class)->findAll();

        foreach ($regionList as $region) {
            $result[$region->getNom()] = $region;
        }
        return $result;
    }

    private function exportInExcel($sheet, $spots) {
        $this->generateFirstLineExcel($sheet);
        $numLine=2;
        /** @var $spot Spot */
        foreach ($spots as $spot) {
            $columnLetter = 'A';
            $sheet->setCellValue($columnLetter.$numLine, $spot->getName());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getCodeSpot());
            $columnLetter++;
            if ($spot->getRegion() !=null) {
                $sheet->setCellValue($columnLetter.$numLine, $spot->getRegion()->getNom());
                $columnLetter++;
                $sheet->setCellValue($columnLetter . $numLine, $spot->getRegion()->getCodeRegion());
            } else {
                $columnLetter++;
            }
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getNoteGeneral());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $this->cleanExportExcel($spot->getShortDescription()));
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $this->cleanExportExcel($spot->getDescription()));
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $this->cleanExportExcel($spot->getDescRoute()));
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getKmFromParis());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getKmAutorouteFromParis());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getTimeFromParis());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getPriceAutorouteFromParis());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $this->cleanExportExcel($spot->getDescMaree()));
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $this->cleanExportExcel($spot->getDescOrientationVent()));
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getIsFoil());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getDescFoil());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getDescWave());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getIsContraintEte());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getDescContraintEte());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getGpsLong());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getGpsLat());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getUrlWindFinder());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getURLWindguru());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getURLMeteoFrance());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getURLMeteoConsult());
            $columnLetter++;
            //$sheet->setCellValue('R'.$numLine, $spot->getURLAlloSurf());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getURLMerteo());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getURLTempWater());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getUrlWebcam());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getUrlBalise());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $spot->getURLMaree());
            $columnLetter++;

            $this->exportMareeInExcel($sheet, $spot, $columnLetter, $numLine);
            $columnLetter=$this->getShiftColumnLetter($columnLetter,4);
            $this->exportWindOrientationInExcel($sheet, $spot, $columnLetter, $numLine);
            $columnLetter=$this->getShiftColumnLetter($columnLetter,16);

            $this->exportWebsiteInfoInExcel($sheet, $spot, $columnLetter, $numLine);

            $numLine++;
        }
    }

    private function cleanExportExcel($valueBD) {
        $valueGood = html_entity_decode($valueBD, ENT_QUOTES, "UTF-8");
        $valueGood= str_replace("&agrave;","à",$valueGood);
        $valueGood= str_replace("\n","",$valueGood);
        $valueGood= str_replace("\r","",$valueGood);
        return $valueGood;
    }

    /*
     * $spot->getMareeRestriction()
     * sortie: 0->8
     */
    private function exportMareeInExcel($sheet, Spot $spot, $columnLetter, $numLine) {
        foreach ($spot->getMareeRestriction() as $mareeRestriction) {
            $sheet->setCellValue($this->getShiftMareeEstriction($mareeRestriction,$columnLetter) . $numLine,
                $mareeRestriction->getHauteurMin().'->'.$mareeRestriction->getHauteurMax());
        }
    }

    private function exportWindOrientationInExcel($sheet, Spot $spot, $columnLetter, $numLine) {
        foreach ($spot->getWindOrientation() as $windOrientation) {
            $sheet->setCellValue($this->getShiftWindOrientation($windOrientation,$columnLetter).$numLine, WebsiteGetData::transformeOrientationStateToValue($windOrientation->getState()));
        }
    }

    private function exportWebsiteInfoInExcel($sheet, Spot $spot, $columnLetter, $numLine) {
        foreach ($spot->getWebSiteInfos() as $websiteInfo) {
            $sheet->setCellValue($columnLetter.$numLine, $websiteInfo->getUrl());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $websiteInfo->getName());
            $columnLetter++;
            $sheet->setCellValue($columnLetter.$numLine, $websiteInfo->getDescription());
            $columnLetter++;
        }
    }
    private function generateFirstLineExcel($sheet)
    {
        $columnLetter = 'A';
        $sheet->setCellValue($columnLetter.'1', 'Nom');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'CodeSpot');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Region');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'CodeRegion');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Note');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'ShortDescription');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Description');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Localisation');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'DistFromParis');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'DistFromParisAutoroute');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'TimeFromParis');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'PéageFromParis');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'MareeDesc');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'OrientationDesc');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'IsFoil');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'FoilDesc');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'WaveDesc');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'IsContraintEte');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'ContraintEteDesc');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Long');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Lat');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Windfinder');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Windguru');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Meteo France');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'MeteoConsult');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'AlloSurf');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Merteo');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Temp eau');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Webcam');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Balise');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Maree');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'top');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'OK');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'warn');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'KO');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'n');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'nne');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'ne');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'ene');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'e');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'ese');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'se');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'sse');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 's');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'ssw');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'sw');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'wsw');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'w');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'wnw');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'nw');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'nnw');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'URL1');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Titre1');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Commentaire1');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'URL2');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Titre2');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Commentaire2');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'URL3');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Titre3');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Commentaire3');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'URL4');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Titre4');
        $columnLetter++;
        $sheet->setCellValue($columnLetter.'1', 'Commentaire4');
    }

    private function getShiftMareeEstriction(MareeRestriction $mareeRestriction, $columnLetter) {
        switch ($mareeRestriction->getState()) {
            case 'KO' : $columnLetter++;
            case 'warn' : $columnLetter++;
            case 'OK' : $columnLetter++;
            //case 'top' : return $columnLetter;
        }
        return $columnLetter;
    }

    private function getShiftWindOrientation(WindOrientation $windOrientation, $columnLetter) {
        switch ($windOrientation->getOrientation()) {
            case 'nnw' : $columnLetter++;
            case 'nw' : $columnLetter++;
            case 'wnw' : $columnLetter++;
            case 'w' : $columnLetter++;
            case 'wsw' : $columnLetter++;
            case 'sw' : $columnLetter++;
            case 'ssw' : $columnLetter++;
            case 's' : $columnLetter++;
            case 'sse' : $columnLetter++;
            case 'se' : $columnLetter++;
            case 'ese' : $columnLetter++;
            case 'e' : $columnLetter++;
            case 'ene' : $columnLetter++;
            case 'ne' : $columnLetter++;
            case 'nne' : $columnLetter++;
            //case 'n' : $columnLetter++;
        }
        return $columnLetter;
    }

    private function getShiftColumnLetter($columnLetter, $nbShift) {
        for ($i=0;$i<$nbShift;$i++) {
            $columnLetter++;
        }
        return $columnLetter;
    }
}