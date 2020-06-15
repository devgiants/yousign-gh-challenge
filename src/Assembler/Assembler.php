<?php

declare(strict_types=1);


namespace App\Assembler;


use Doctrine\ORM\EntityManagerInterface;

abstract class Assembler
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * Assembler constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
