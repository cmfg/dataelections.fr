<?php

/*
 * Copyright 2015 Guillaume Royer
 *
 * This file is part of DataElections.
 *
 * DataElections is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * DataElections is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with DataElections. If not, see <http://www.gnu.org/licenses/>.
 */

namespace AppBundle\Domain\Election\Entity\Candidat;

use AppBundle\Domain\Election\Entity\Election\Election;

class ListeCandidate extends Candidat
{
    /**
     * Le nom de la liste.
     *
     * @var string
     */
    private $nom;

    /**
     * Constructeur d'objet personne.
     *
     * @param string $nom Le nom de la liste.
     */
    public function __construct(Election $election, $nuance, $nom)
    {
        \Assert\that($nom)->string();

        $this->election = $election;
        $this->nom = $nom;
        $this->nuance = (string) $nuance;
    }

    /**
     * Retourne le nom de l'élection.
     *
     * @return string Le nom de l'élection.
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Retourne le nom de l'élection.
     *
     * @return string Le nom de l'élection.
     */
    public function __toString()
    {
        return $this->getNom();
    }
}
