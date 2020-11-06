<?php

namespace App\Data;

use App\Entity\Campus;
use DateTime;

class SearchData
{
    /**
     * @var Campus
     */
    public $campus;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var DateTime
     */
    public $dateDebut;

    /**
     * @var DateTime
     */
    public $dateFin;

    /**
     * @var boolean
     */
    public $estOrganisateur;

    /**
     * @var boolean
     */
    public $estInscrit;

    /**
     * @var boolean
     */
    public $pasInscrit;

    /**
     * @var boolean
     */
    public $sortiePassee;
}
