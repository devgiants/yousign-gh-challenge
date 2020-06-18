<?php

declare(strict_types=1);


namespace App\Assembler;


use App\Dto\Commit;
use App\Dto\DtoInterface;
use App\Entity\EntityInterface;
use App\Entity\Commit as CommitEntity;
use App\Entity\GithubRepo;
use Symfony\Component\Validator\Exception\InvalidArgumentException;


class CommitAssembler extends Assembler implements AssemblerInterface
{
    /**
     * @inheritDoc
     */
    public function getDTO(DtoInterface $dto, EntityInterface $entity, array $params = []): DtoInterface
    {
        // TODO: Implement getDTO() method.
    }

    /**
     * @inheritDoc
     */
    public function getEntity(string $entityFqcn, DtoInterface $dto, array $params = []): EntityInterface
    {
        if ($entityFqcn !== CommitEntity::class || !($dto instanceof Commit)) {
            throw new InvalidArgumentException("This assembler is for commit only");
        }

        if (!isset($params['repo']) || !($params['repo'] instanceof GithubRepo)) {
            throw new InvalidArgumentException("Miss the repo needed for commit retrieval");
        }

        $commitEntity = $this->entityManager->getRepository($entityFqcn)->findOneBy(
            [
                'sha' => $dto->getSha(),
                'githubRepo' => $params['repo'],
                'pushId' => $params['push_id'],
            ]
        );

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
