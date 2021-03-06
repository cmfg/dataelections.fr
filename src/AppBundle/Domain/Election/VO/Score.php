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

namespace AppBundle\Domain\Election\VO;

class Score
{
    /**
     * Le score nombre de voix.
     *
     * @var int
     */
    private $voix;

    /**
     * Le score en pourcentage des exprimés.
     *
     * @var float
     */
    private $pourcentage;

    /**
     * Créer un objet Score.
     *
     * @param int $pourcentage Le score en pourcentage des exprimés.
     *
     * @return Score Un nouvel object Score.
     */
    public static function fromPourcentage($pourcentage)
    {
        $score = new self();

        $score->pourcentage = $pourcentage;

        return $score;
    }

    /**
     * Créer un objet Score.
     *
     * @param int $pourcentage Le score en pourcentage des exprimés.
     * @param int $exprimes    Le nombre de suffrages exprimés.
     *
     * @return Score Un nouvel object Score.
     */
    public static function fromPourcentageAndExprimes($pourcentage, $exprimes)
    {
        $score = new self();

        $score->pourcentage = $pourcentage;
        $score->voix = round($exprimes * $pourcentage / 100);

        return $score;
    }

    /**
     * Créer un objet Score contenant juste un nombre de voix.
     *
     * @param int $voix Le nombre de voix.
     *
     * @return Score Un nouvel object Score.
     */
    public static function fromVoix($voix)
    {
        $score = new self();

        $score->voix = $voix;
        $score->pourcentage = null;

        return $score;
    }

    /**
     * Créer un objets Score.
     *
     * @param int $voix     Le nombre de voix fait à l'élection.
     * @param int $exprimes Le nombre de suffrage exprimés à l'élection.
     *
     * @return Score Un nouvel object Score.
     */
    public static function fromVoixAndExprimes($voix, $exprimes)
    {
        $score = new self();

        $score->voix = $voix;

        if ($exprimes) {
            $score->pourcentage = ($voix / $exprimes) * 100;
        }

        return $score;
    }

    /**
     * Récupérer le score en pourcentage.
     *
     * @return float Le score en pourcentage.
     */
    public function toPourcentage()
    {
        return $this->pourcentage;
    }

    /**
     * Récupérer le score en nombre de voix.
     *
     * @return int Le scre en nombre de voix.
     */
    public function toVoix()
    {
        return $this->voix;
    }
}
