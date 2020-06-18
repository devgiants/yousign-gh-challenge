<?php

declare(strict_types=1);


namespace App\Assembler;


use App\Dto\DtoInterface;
use App\Dto\GithubRepo;
use App\Entity\GithubRepo as GithubRepoEntity;
use App\Entity\EntityInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class GithubRepoAssembler extends Assembler implements AssemblerInterface
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
        $wasNew = false;

        if ($entityFqcn !== GithubRepoEntity::class || !($dto instanceof GithubRepo)) {
            throw new InvalidArgumentException("This assembler is for repo only");
        }

        $githubRepoEntity = $this->entityManager->getRepository($entityFqcn)->findOneBy(['githubId' => $dto->getId()]);

        // Creates if not exists
        if (!$githubRepoEntity instanceof GithubRepoEntity) {
            $githubRepoEntity = new GithubRepoEntity();
            $this->entityManager->persist($githubRepoEntity);
            $wasNew = true;
        }

        // Mapping
        $githubRepoEntity
            ->setGithubId($dto->getId())
            ->setUrl($dto->getUrl())
            ->setName($dto->getName())
        ;

        if ($wasNew) {
            // Must flush each time to ensure repo will be find next time, even if it is inside a same batch
            $this->entityManager->flush();
        }

        return $githubRepoEntity;
    }
}
