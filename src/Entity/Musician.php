<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MusicianRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class Musician implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Skill", mappedBy="musician")
     */
    private $skills;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Job", mappedBy="musician")
     */
    private $jobs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Education", mappedBy="musician")
     */
    private $education;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\JobToBeOffered", mappedBy="musician")
     */
    private $jobstobeoffered;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Location", mappedBy="musician")
     */
    private $locations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $currentsalary;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $expectedsalary;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="musician")
     */
    private $projects;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->education = new ArrayCollection();
        $this->jobstobeoffered = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function __toString() {
        return $this->username;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Skill[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
            $skill->setMusician($this);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
            // set the owning side to null (unless already changed)
            if ($skill->getMusician() === $this) {
                $skill->setMusician(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setMusician($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->contains($job)) {
            $this->jobs->removeElement($job);
            // set the owning side to null (unless already changed)
            if ($job->getMusician() === $this) {
                $job->setMusician(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Education[]
     */
    public function getEducation(): Collection
    {
        return $this->education;
    }

    public function addEducation(Education $education): self
    {
        if (!$this->education->contains($education)) {
            $this->education[] = $education;
            $education->setMusician($this);
        }

        return $this;
    }

    public function removeEducation(Education $education): self
    {
        if ($this->education->contains($education)) {
            $this->education->removeElement($education);
            // set the owning side to null (unless already changed)
            if ($education->getMusician() === $this) {
                $education->setMusician(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|JobToBeOffered[]
     */
    public function getJobstobeoffered(): Collection
    {
        return $this->jobstobeoffered;
    }

    public function addJobstobeoffered(JobToBeOffered $jobstobeoffered): self
    {
        if (!$this->jobstobeoffered->contains($jobstobeoffered)) {
            $this->jobstobeoffered[] = $jobstobeoffered;
            $jobstobeoffered->setMusician($this);
        }

        return $this;
    }

    public function removeJobstobeoffered(JobToBeOffered $jobstobeoffered): self
    {
        if ($this->jobstobeoffered->contains($jobstobeoffered)) {
            $this->jobstobeoffered->removeElement($jobstobeoffered);
            // set the owning side to null (unless already changed)
            if ($jobstobeoffered->getMusician() === $this) {
                $jobstobeoffered->setMusician(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Location[]
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setMusician($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->contains($location)) {
            $this->locations->removeElement($location);
            // set the owning side to null (unless already changed)
            if ($location->getMusician() === $this) {
                $location->setMusician(null);
            }
        }

        return $this;
    }

    public function getCurrentsalary(): ?string
    {
        return $this->currentsalary;
    }

    public function setCurrentsalary(?string $currentsalary): self
    {
        $this->currentsalary = $currentsalary;

        return $this;
    }

    public function getExpectedsalary(): ?string
    {
        return $this->expectedsalary;
    }

    public function setExpectedsalary(?string $expectedsalary): self
    {
        $this->expectedsalary = $expectedsalary;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getPhoto(): ?string
    {
        $url = "http://localhost:8000/uploads/photos/$this->photo";

        return $url;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setMusician($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getMusician() === $this) {
                $project->setMusician(null);
            }
        }

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }
}
