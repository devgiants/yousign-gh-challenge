<?php

declare(strict_types=1);


namespace App\Assembler;


use App\Dto\DtoInterface;
use App\Entity\EntityInterface;

interface AssemblerInterface
{
    /**
     * @param DtoInterface $dto
     * @param EntityInterface $entity
     * @param array $params Context params, if needed
     * @return DtoInterface
     */
    public function getDTO(DtoInterface $dto, EntityInterface $entity, array $params = []): DtoInterface;


    /**
     * @param string $entityFqcn
     * @param DtoInterface $dto
     * @param array $params Context params, if needed
     * @return EntityInterface
     */
    public function getEntity(string $entityFqcn, DtoInterface $dto, array $params = []): EntityInterface;
}
