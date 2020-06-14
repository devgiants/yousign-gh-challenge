<?php


namespace App\Assembler;


use App\Dto\Commit;
use App\Dto\DtoInterface;
use App\Entity\EntityInterface;
use App\Entity\Commit as CommitEntity;
use Symfony\Component\Validator\Exception\InvalidArgumentException;


class CommitAssembler extends Assembler implements AssemblerInterface
{
    /**
     * @inheritDoc
     */
    public function getDTO(DtoInterface $dto, EntityInterface $entity): DtoInterface
    {
        // TODO: Implement getDTO() method.
    }

    /**
     * @inheritDoc
     */
    public function getEntity(string $entityFqcn, DtoInterface $dto): EntityInterface
    {
        if ($entityFqcn !== CommitEntity::class || !($dto instanceof Commit)) {
            throw new InvalidArgumentException("This assembler is for commit only");
        }

        $commitEntity = $this->entityManager->getRepository($entityFqcn)->find($dto->getSha());

        // Creates if not exists
        if (!$commitEntity instanceof CommitEntity) {
            $commitEntity = new CommitEntity();
            $this->entityManager->persist($commitEntity);
        }

        // Mapping
        $commitEntity
            ->setSha($dto->getSha())
            ->setMessage($dto->getMessage())
        ;

        return $commitEntity;
    }
}
