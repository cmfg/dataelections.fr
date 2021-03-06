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

namespace AppBundle\Repository\CacheInfo;

use AppBundle\Domain\Territoire\Entity\Territoire\AbstractTerritoire;
use AppBundle\Domain\Territoire\Entity\Territoire\CirconscriptionEuropeenne;
use AppBundle\Domain\Territoire\Entity\Territoire\Commune;
use AppBundle\Domain\Territoire\Entity\Territoire\Departement;
use AppBundle\Domain\Territoire\Entity\Territoire\Region;

class DoctrineCacheInfoRepository
{
    /**
     * On garde les timestamp à persister pour éviter
     * les erreurs sur la contrainte d'unicité si invalidate()
     * est appelée plusieurs fois pour le même territoire entre
     * deux flush.
     *
     * @var \SplObjectStorage
     */
    private $toPersist;

    private $cacheInvalidateDate;

    public function __construct($doctrine, $cacheInvalidateDate)
    {
        $this->em = $doctrine->getManager();
        $this->toPersist = new \SplObjectStorage();
        $this->cacheInvalidateDate =
            (new \DateTime())
            ->setTimestamp((int) $cacheInvalidateDate);

        if ($this->cacheInvalidateDate > new \DateTime()) {
            throw new \InvalidArgumentException(
                'La date de dernière'.
                'invalidation du cache ne peut être supérieure'.
                'à la date actuelle.'
            );
        }
    }

    public function getCacheInvalidateDate()
    {
        return $this->cacheInvalidateDate;
    }

    public function getLastModified(AbstractTerritoire $territoire)
    {
        $timestamp = $this
            ->em
            ->getRepository(
                'AppBundle\Repository\CacheInfo\TerritoireTimestamp'
            )
            ->findOneByTerritoire($territoire)
        ;

        if ($timestamp && $timestamp->getTimestamp() > $this->cacheInvalidateDate) {
            return $timestamp->getTimestamp();
        }

        return $this->cacheInvalidateDate;
    }

    public function invalidate(AbstractTerritoire $territoire)
    {
        $timestamp = $this
            ->em
            ->getRepository(
                'AppBundle\Repository\CacheInfo\TerritoireTimestamp'
            )
            ->findOneByTerritoire($territoire)
        ;

        if (!$timestamp && $this->toPersist->offsetExists($territoire)) {
            $timestamp = $this->toPersist[$territoire];
        }

        if ($timestamp) {
            $timestamp->setNow();
        }

        if (!$timestamp) {
            $timestamp = new TerritoireTimestamp($territoire);
            $this->em->persist($timestamp);
            $this->toPersist[$territoire] = $timestamp;
        }

        if ($territoire instanceof Commune) {
            $this->invalidate($territoire->getDepartement());

            return;
        }

        if ($territoire instanceof Departement) {
            $this->invalidate($territoire->getRegion());

            return;
        }

        if ($territoire instanceof Region && $territoire->getCirconscriptionEuropeenne()) {
            $this->invalidate($territoire->getCirconscriptionEuropeenne());
        }

        if ($territoire instanceof Region || $territoire instanceof CirconscriptionEuropeenne) {
            $this->invalidate($territoire->getPays());
        }
    }
}
