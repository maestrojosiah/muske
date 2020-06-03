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

    private $photourl;
    private $thumbnailurl;
    private $logourl;
    private $isproandactive;
    private $ismuskeandactive;
    private $hassettings;
    private $realemail;
    private $realphone;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Settings", mappedBy="musician", cascade={"persist", "remove"})
     */
    private $settings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gallery", mappedBy="musician")
     */
    private $uploadedphotos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="musician")
     */
    private $documents;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Pro", mappedBy="musician", cascade={"persist", "remove"})
     */
    private $pro;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $subscribed;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $confirmed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pdfTheme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $webTheme;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rating", mappedBy="musician")
     */
    private $ratings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="musician")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Track::class, mappedBy="musician")
     */
    private $tracks;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->education = new ArrayCollection();
        $this->jobstobeoffered = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->uploadedphotos = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->tracks = new ArrayCollection();
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
        if($this->isProAndActive()){
            return $this->email;
        } else {
            return "musician@muske.co.ke";
        }
        
    }

    public function getRealEmail(): ?string
    {
        $this->realemail = $this->email;
        return $this->realemail;
        
    }


    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        if($this->isProAndActive()){
            return $this->phone;
        } else {
            return "Call MuSKe | 0705285959";
        }
    }

    public function getRealPhone(): ?string
    {
        $this->realphone = $this->phone;
        return $this->realphone;
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

        return $this->photo;
    }

    public function photourl(): ?string
    {
        $url = "https://muske.co.ke/uploads/photos/$this->photo";
        $this->photourl = $url;

        return $this->photourl;
    }


    public function thumbnailurl(): ?string
    {
        $url = "https://muske.co.ke/uploads/photos/thumbs/".$this->photo.".png";
        $this->thumbnailurl = $url;

        return $this->thumbnailurl;
    }

    public function logourl(): ?string
    {
        $url = "https://muske.co.ke/img/logo_only_white_sq.png";
        $this->logourl = $url;

        return $this->logourl;
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

    public function getSettings(): ?Settings
    {
        return $this->settings;
    }

    public function setSettings(?Settings $settings): self
    {
        $this->settings = $settings;

        // set (or unset) the owning side of the relation if necessary
        $newMusician = null === $settings ? null : $this;
        if ($settings->getMusician() !== $newMusician) {
            $settings->setMusician($newMusician);
        }

        return $this;
    }

    /**
     * @return Collection|Gallery[]
     */
    public function getUploadedphotos(): Collection
    {
        return $this->uploadedphotos;
    }

    public function addUploadedphoto(Gallery $uploadedphoto): self
    {
        if (!$this->uploadedphotos->contains($uploadedphoto)) {
            $this->uploadedphotos[] = $uploadedphoto;
            $uploadedphoto->setMusician($this);
        }

        return $this;
    }

    public function removeUploadedphoto(Gallery $uploadedphoto): self
    {
        if ($this->uploadedphotos->contains($uploadedphoto)) {
            $this->uploadedphotos->removeElement($uploadedphoto);
            // set the owning side to null (unless already changed)
            if ($uploadedphoto->getMusician() === $this) {
                $uploadedphoto->setMusician(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setMusician($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getMusician() === $this) {
                $document->setMusician(null);
            }
        }

        return $this;
    }

    public function getPro(): ?Pro
    {
        return $this->pro;
    }

    public function setPro(?Pro $pro): self
    {
        $this->pro = $pro;

        // set (or unset) the owning side of the relation if necessary
        $newMusician = null === $pro ? null : $this;
        if ($pro->getMusician() !== $newMusician) {
            $pro->setMusician($newMusician);
        }

        return $this;
    }

    public function hasSettings(){
        if($this->getSettings()){
            $fact = true;
        } else {
            $fact = false;
        }
        
        $this->hassettings = $fact;
        return $this->hassettings;
    }

    public function isProAndActive(){
        if($this->getSettings() && $this->getSettings()->getPro() == 'true'){
            $ending = $this->getPro()->getEnding();
            $now = new \DateTime("now");
            $fact = false;
            if($ending > $now){
                $fact = true;
            }
        } else {
            $fact = false;
        }
        
        $this->isproandactive = $fact;
        return $this->isproandactive;
    }

    public function isMuskeAndActive(){
        if($this->getSettings()){
            $muske = $this->getSettings()->getMuske();
            $fact = false;
            if($muske == "true"){
                $fact = true;
            }
        } else {
            $fact = false;
        }
        
        $this->ismuskeandactive = $fact;
        return $this->ismuskeandactive;
    }

    public function getSubscribed(): ?string
    {
        return $this->subscribed;
    }

    public function setSubscribed(?string $subscribed): self
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    public function getConfirmed(): ?string
    {
        return $this->confirmed;
    }

    public function setConfirmed(?string $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getPdfTheme(): ?string
    {
        return $this->pdfTheme;
    }

    public function setPdfTheme(?string $pdfTheme): self
    {
        $this->pdfTheme = $pdfTheme;

        return $this;
    }

    public function getWebTheme(): ?string
    {
        return $this->webTheme;
    }

    public function setWebTheme(?string $webTheme): self
    {
        $this->webTheme = $webTheme;

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setMusician($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getMusician() === $this) {
                $rating->setMusician(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setMusician($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getMusician() === $this) {
                $post->setMusician(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Track[]
     */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks[] = $track;
            $track->setMusician($this);
        }

        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->tracks->contains($track)) {
            $this->tracks->removeElement($track);
            // set the owning side to null (unless already changed)
            if ($track->getMusician() === $this) {
                $track->setMusician(null);
            }
        }

        return $this;
    }

}
