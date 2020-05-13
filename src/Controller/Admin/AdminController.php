<?php

namespace App\Controller\Admin;

use App\Form\GalleryType;
use App\Form\JobType;
use App\Form\RoleType;
use App\Form\EducationType;
use App\Form\SpecialtyType;
use App\Form\SkillType;
use App\Form\ProjectType;
use App\Form\JobToBeOfferedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OrganizeThings; 

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{


    /**
     * @Route("/musician/index/{entity}", name="musician_admin_index", methods={"GET"})
     */
    public function index(OrganizeThings $organizeThings, $entity = ''): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $musician = $this->getUser();
        $title = ucfirst($entity);

        switch ($entity) {
            case 'jobs':
                $entity = $organizeThings->organizedJobsAccordingToSettings($musician);
                break;
            case 'roles':
                $jobs = $organizeThings->organizedJobsAccordingToSettings($musician);
                $entity = [];
                foreach ($jobs as $job) {
                    $roles = $job->getRoles();
                    foreach ($roles as $role ) {
                        $entity[] = $role;
                    }
                }
                break;
            case 'education':
                $entity = $organizeThings->organizedEducationAccordingToSettings($musician);
                break;
            case 'specialties':
                $educ = $organizeThings->organizedEducationAccordingToSettings($musician);
                $entity = [];
                foreach ($educ as $edu) {
                    $pecialties = $edu->getSpecialties();
                    foreach ($pecialties as $spec ) {
                        $entity[] = $spec;
                    }
                }
                break;    
            case 'skills':
                $entity = $musician->getSkills();
                break;
            case 'projects':
                $entity = $musician->getProjects();
                break;
            case 'myJobs':
                $entity = $musician->getJobsToBeOffered();
                break;
            case 'photos':
                $entity = $musician->getUploadedPhotos();
                break;
            default:
                $entity = $musician->getJobs();
                break;
        }

        $sidebar_menu = [ 'Job Experience'=>['jobs', 'roles'], 'Education'=>['education', 'specialties'], 'skills', 'projects', 'myJobs', 'photos' ];

        return $this->render('admin/index.html.twig', [
            'musician' => $musician,
            'sidebar_menu' => $sidebar_menu,
            'entity' => $entity,
            'title' => $title,
        ]);
    }

    /**
     * @Route("/edit/{entity_name}/{id}", name="admin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, $entity_name, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $usable_entity_name = rtrim(ucfirst($entity_name), "s");
        $musician = $this->getUser();
        switch ($entity_name) {
            case 'jobs':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Job")->find($id);
                $form = $this->createForm(JobType::class, $entity);
                break;
            case 'skills':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Skill")->find($id);
                $form = $this->createForm(SkillType::class, $entity);
                break;
            case 'photos':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Gallery")->find($id);
                $form = $this->createForm(GalleryType::class, $entity);
                break;
            case 'roles':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Role")->find($id);
                $form = $this->createForm(RoleType::class, $entity);
                break;
            case 'education':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Education")->find($id);
                $form = $this->createForm(EducationType::class, $entity);
                break;
            case 'specialties':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Specialty")->find($id);
                $form = $this->createForm(SpecialtyType::class, $entity);
                break;
            case 'projects':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Project")->find($id);
                $form = $this->createForm(ProjectType::class, $entity);
                break;
            case 'myJobs':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:JobToBeOffered")->find($id);
                $form = $this->createForm(JobToBeOfferedType::class, $entity);
                break;
            default:
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Skill")->find($id);
                $form = $this->createForm(SkillType::class, $entity);
                break;
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('musician_admin_index', ['entity' => $entity_name]);
        }

        $sidebar_menu = [ 'Job Experience'=>['jobs', 'roles'], 'Education'=>['education', 'specialties'], 'skills', 'projects', 'myJobs', 'photos' ];

        return $this->render('admin/edit.html.twig', [
            'entity' => $entity,
            'musician' => $musician,
            'form' => $form->createView(),
            'usable_entity_name' => $usable_entity_name,
            'sidebar_menu' => $sidebar_menu,
        ]);
    }

    /**
     * @Route("/delete/{entity_name}/{id}", name="admin_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $entity_name, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        switch ($entity_name) {
            case 'jobs':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Job")->find($id);
                break;
            case 'skills':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Skill")->find($id);
                break;
            case 'photos':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Gallery")->find($id);
                $current_photo_path = $this->getParameter('gallery_directory')."/".$entity->getPhoto();
                $current_photo_thumb_path = $this->getParameter('gallery_directory')."/thumbs/".$entity->getPhoto().".png";
                unlink($current_photo_path);
                unlink($current_photo_thumb_path);
                break;
            case 'roles':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Role")->find($id);
                break;
            case 'education':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Education")->find($id);
                break;
            case 'specialties':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Specialty")->find($id);
                break;
            case 'projects':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Project")->find($id);
                break;
            case 'myJobs':
                $entity = $this->getDoctrine()->getManager()->getRepository("App:JobToBeOffered")->find($id);
                break;
            default:
                $entity = $this->getDoctrine()->getManager()->getRepository("App:Skill")->find($id);
                break;
        }
        
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($entity);
            $entityManager->flush();

        }

        return $this->redirectToRoute('musician_admin_index', ['entity' => $entity_name]);
    }


}