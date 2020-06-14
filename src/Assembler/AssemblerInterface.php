<?php


namespace App\Assembler;


use App\Dto\DtoInterface;
use App\Entity\EntityInterface;

interface AssemblerInterface
{
    /**
     * @param DtoInterface $dto
     * @param EntityInterface $entity
     * @return DtoInterface
     */
    public function getDTO(DtoInterface $dto, EntityInterface $entity): DtoInterface;


    /**
     * @param string $entityFqcn
     * @param DtoInterface $dto
     * @return EntityInterface
     */
    public function getEntity(string $entityFqcn, DtoInterface $dto): EntityInterface;
}
