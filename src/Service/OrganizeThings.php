<?php 

namespace App\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrganizeThings extends AbstractController
{
    private $jobs;
    private $educ;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->educ = new ArrayCollection();
    }

    public function organizedJobsAccordingToSettings($musician)
    {
        if($musician->getSettings()){
            $jobOrder = $musician->getSettings()->getJobOrder() ? $musician->getSettings()->getJobOrder() : "id";
            $jobSort = $musician->getSettings()->getJobOrderBy() ? $musician->getSettings()->getJobOrderBy() : "ASC";
            $this->jobs = $this->getDoctrine()->getManager()->getRepository('App:Job')
            ->findByGivenField($jobOrder, $jobSort, $musician);
        } else {
            $this->jobs = $musician->getJobs();
        }

        return $this->jobs;

    }

    public function organizedEducationAccordingToSettings($musician)
    {
        if($musician->getSettings()){
            $eduOrder = $musician->getSettings()->getEduOrder() ? $musician->getSettings()->getEduOrder() : "id";
            $eduSort = $musician->getSettings()->getEduOrderBy() ? $musician->getSettings()->getEduOrderBy() : "ASC";
            $this->educ = $this->getDoctrine()->getManager()->getRepository('App:Education')
            ->findByGivenField($eduOrder, $eduSort, $musician);

        } else {
            $this->educ = $musician->getEducation();
        }

        return $this->educ;

    }

}