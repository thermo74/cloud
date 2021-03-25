<?php

namespace App\Controller\Admin;

use App\Entity\Files;
use App\Form\FilesType;
use App\Repository\FilesRepository;
use App\Service\FilePath;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @Route("/{_locale<%app.supported_locales%>}/admin/files")
 */
class AdminFilesController extends AbstractController
{

    private $fileUploader;
    private $filePath;
    private $resizer;

    public function __construct(FileUploader $fileUploader, FilePath $filePath, ImageResizer $resizer)
    {
        $this->fileUploader = $fileUploader;
        $this->filePath = $filePath;
        $this->resizer = $resizer;
    }

    /**
     * @Route("/", name="files_index", methods={"GET"})
     * @param FilesRepository $filesRepository
     * @return Response
     */
    public function index(FilesRepository $filesRepository): Response
    {
        return $this->render('admin/files/index.html.twig', [
            'files' => $filesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="files_new", methods={"GET","POST"})
     * @param Request $request
     * @param string $filesDir
     * @return Response
     */
    public function new(Request $request, string $filesDir): Response
    {
        $file = new Files();
        $form = $this->createForm(FilesType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $uploadedFile = $form->get('file')->getData()) {

            $date = new DateTime('NOW');
            $year = $date->format('Y');
            $month = $date->format('m');
            $day = $date->format('d');

            $uploadDir = "{$filesDir}/{$year}/{$month}/{$day}/";

            $slugger = new AsciiSlugger();
            $name = $form->get('name')->getData();
            $filename = $slugger->slug($name);
            $size = $uploadedFile->getSize();
            $mime = $uploadedFile->getMimeType();
            $fileExtension = $uploadedFile->guessExtension();
            $uploader = $this->fileUploader->upload($uploadedFile, $uploadDir, false, $filename);
            if ($uploader){
                $path = $uploader['path'];
                if (strpos($mime, 'image') !== false){
                    $resize = $this->resizer->thuhmbnail($path);
                }else{
                    $resize = true;
                }
                if (!$resize){
                    unlink($uploadDir.$filename.'.'.$fileExtension);
                    $this->addFlash('danger', 'Il y a eu une erreur durant la génération du thumbnail de l\'image veuillez réessayer. Si le problème persiste contacter we studio');
                }else{

                        $file->setName($name);
                        $file->setUploadDate($date);
                        $file->setMime($mime);
                        $file->setSize($size);
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($file);
                        $entityManager->flush();
                }
            }else{
                $this->addFlash('danger', 'Un fichier avec le même nom existe déjà à la date d\'aujourd\'hui veuillez utiliser un autre nom ou attendez demain.');
            }
            return $this->redirectToRoute('files_index');
        }

        return $this->render('admin/files/new.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="files_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Files $file
     * @param string $filesDir
     * @return Response
     */
    public function edit(Request $request, Files $file, string $filesDir): Response
    {
        $form = $this->createForm(FilesType::class, $file);
        $form->get('name')->setData($file->getName());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();
            $date = new DateTime('NOW');
            $path = $this->filePath->get($file->getId());
            if ($uploadedFile){
                if (file_exists($path)){
                    unlink($path);
                }else{
                    $this->addFlash('danger', 'Sorry, old file can\'t be deleted');
                }

                $thumbnailPath = $this->filePath->getThumbnail($file->getId());
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }else{
                    $this->addFlash('danger', 'Thumbnail can\'t be deleted');
                }
                $year = $date->format('Y');
                $month = $date->format('m');
                $day = $date->format('d');
                $uploadDir = "{$filesDir}/{$year}/{$month}/{$day}/";
                $slugger = new AsciiSlugger();
                $filename = $slugger->slug($file->getName());
                $size = $uploadedFile->getSize();
                $mime = $uploadedFile->getMimeType();
                $uploader = $this->fileUploader->upload($uploadedFile, $uploadDir, false, $filename);
                if ($uploader){
                    $path = $uploader['path'];
                    $resize = true;
                    if (strpos($mime, 'image') !== false){
                        $resize = $this->resizer->thuhmbnail($path);
                    }
                    if (!$resize){
                        unlink($path);
                        $this->addFlash('danger', 'An error occurred on thumbnail generation please retry');
                    }else{
                        $file->setUploadDate($date);
                        $file->setMime($mime);
                        $file->setSize($size);
                    }
                }else{
                    $this->addFlash('danger', 'An error occurred on file upload');
                }
            }
            $name = $form->get('name')->getData();
            if ($name) {
                if ($path){
                    $newPath = $this->filePath->generate($name, $file->getUploadDate(), $file->getMime());
                    $thumbnailPath = $this->filePath->getThumbnail($file->getId());
                    $newThumbnailPath = $this->filePath->generateThumbnail($name, $file->getUploadDate(), $file->getMime());
                    $fileSystem = new Filesystem();
                    if(!file_exists($newPath) && !file_exists($newThumbnailPath)){
                        $fileSystem->rename($path, $newPath);
                        $fileSystem->rename($thumbnailPath, $newThumbnailPath);
                        $file->setName($name);
                    }elseif($path !== $newPath){
                        $this->addFlash('danger', 'A file with this name already exists');
                    }
                }else{
                    $this->addFlash('danger', 'File not found so we can\'t rename it');
                }
            }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

            return $this->redirectToRoute('files_index');
        }

        return $this->render('admin/files/edit.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="files_delete", methods={"DELETE"})
     * @param Request $request
     * @param Files $file
     * @param FilePath $filePath
     * @return Response
     */
    public function delete(Request $request, Files $file, FilePath $filePath): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $path = $filePath->get($file->getId());
            if (file_exists($path)){
                unlink($path);
            }else{
                $this->addFlash('danger', 'Le fichier na pas été trouvée et ne sera plus listé.');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('files_index');
    }

}
