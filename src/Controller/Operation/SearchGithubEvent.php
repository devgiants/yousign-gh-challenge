<?php


namespace App\Controller\Operation;


use Symfony\Component\Routing\Annotation\Route;

class SearchGithubEvent
{
    /**
     * @Route(
     *     name="github_event_search",
     *     path="/event/search",
     *     methods={"POST"},
     *     defaults={
     *         "_api_item_operation_name"="post_publication"
     *     }
     * )
     */
    public function __invoke()
    {
//        $this->bookPublishingHandler->handle($data);

        return [];
    }
}
