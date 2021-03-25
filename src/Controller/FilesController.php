<?php


namespace App\Controller;

use App\Entity\Files;
use App\Service\FilePath;
use App\Service\hasAccess;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Twig\Environment;

/**
 * Class FilesController
 * @package App\Controller
 * @Route({
 * "en" : "/en/profile/files",
 * "fr": "/fr/profil/fichiers"
 * })
 */

class FilesController extends AbstractController
{

    private $entityManager;
    private $twig;

    public function __construct(EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    /**
     * @Route("", name="profile_files")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function files(PaginatorInterface $paginator, Request $request): Response
    {
        $userMail = $this->getUser()->getUsername();
        $user = $this->entityManager->getRepository('App:User')->findOneBy(['email' => $userMail]);
        $subRoles = $user->getSubRoles();
        $search = htmlspecialchars($request->query->get('s'));
        $requestCategories = $request->query->get('categories');
        $categories = array();
        foreach ($subRoles as $role){
            if ($role->getIsActive()){
                $subCats = $role->getCategories();
                foreach ($subCats as $cat){
                    $categories[] = $cat;
                }
            }
        }
        if ($categories){
            $categories = array_unique($categories);
            $categoriesAccess = $categories;
            if ($requestCategories){
                foreach ($categories as $key => $category){
                    if (!in_array($category->getId(), $requestCategories)){
                        unset($categories[$key]);
                    }
                }
            }
            if ($search){
                $query = $this->entityManager->getRepository('App:Files')->findByCategoriesSearchQuery($categories, $search);
            }else{
                $query = $this->entityManager->getRepository('App:Files')->findByCategoriesQuery($categories);
            }
            $pagination = $paginator->paginate(
                $query->getResult(),
                $request->query->getInt('page', 1),
                100
            );
            return $this->render('profile/files/index.html.twig', [
                'pagination' => $pagination,
                'categories' => $categoriesAccess
            ]);
        }
        return $this->render('profile/files/nofile.html.twig');
    }

    /**
     * @Route("/view/{id}/{size}", name="profile_files_view")
     * @param Files $file
     * @param hasAccess $hasAccess
     * @param FilePath $filePath
     * @param string|null $size
     * @return Response
     */
    public function filesView(Files $file, hasAccess $hasAccess, FilePath $filePath, string $size = null): Response
    {
        $access = $hasAccess->index($file, $this->getUser());
        $path = $filePath->get($file->getId());
        if ($access && $path){
            if ($size){
                $fileInfos = pathinfo($path);
                $fileName = $fileInfos['filename'];
                $fileExt = $fileInfos['extension'];
                $fileDir = $fileInfos['dirname'];
                $croppedFile = "{$fileDir}/{$fileName}-{$size}.{$fileExt}";
                if (file_exists($croppedFile)){
                    $path = $croppedFile;
                }
            }
            $mimeTypes = new MimeTypes();
            $response = new BinaryFileResponse($path);
            $slugger = new AsciiSlugger();
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                $slugger->slug($file->getName()).'.'.$mimeTypes->getExtensions($file->getMime())[0]
            );
            $response->headers->set('Content-Type', $file->getMime());
            $response->headers->set('Content-Disposition', $disposition);
        }else{
            $this->addFlash('danger','Vous n\'avez pas accès à ceci.');
            $response = new RedirectResponse($this->generateUrl('profile_files'));
        }
        return $response;
    }

    /**
     * @Route("/download/{id}", name="profile_files_download")
     * @param Files $file
     * @param hasAccess $hasAccess
     * @param FilePath $filePath
     * @return Response
     */
    public function filesDownload(Files $file, hasAccess $hasAccess, FilePath $filePath): Response
    {
        $mimeTypes = new MimeTypes();
        $access = $hasAccess->index($file, $this->getUser());
        $path = $filePath->get($file->getId());

        if ($access && $path){
            $fileContent = file_get_contents($path);
            $slugger = new AsciiSlugger();
            $response = new Response($fileContent);
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $slugger->slug($file->getName()).'.'.$mimeTypes->getExtensions($file->getMime())[0]
            );
            $response->headers->set('Content-Type', $file->getMime());
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('filename', $file->getName().'.'.$mimeTypes->getExtensions($file->getMime())[0]);

            return $response;
        }else{
            return $this->redirectToRoute('profile_files');
        }
    }
}