<?php

namespace App\Service;

use App\Service\ElasticSearch\Repository\PostTypeRepository;

/**
 * Class PostCategoriesService
 *
 * @package App\Service
 */
class PostCategoriesService
{
    /**
     * @var PostTypeRepository
     */
    private $postTypeRepository;

    /**
     * PostCategoriesService constructor.
     *
     * @param PostTypeRepository $postTypeRepository
     */
    public function __construct(PostTypeRepository $postTypeRepository)
    {
        $this->postTypeRepository = $postTypeRepository;
    }

    /**
     * Update category in all posts that contain it.
     *
     * @param array $newCategory
     * @return int Amount of posts that were updated
     */
    public function updatePostsCategory(array $newCategory) : int
    {
        $params = [
            'conflicts' => 'proceed',
            'body' => [
                'query' => [
                    'term' => ['categories.id' => $newCategory['id']]
                ],
                'script' => [
                    'lang' => 'painless',
                    'source' => "
                            ctx._source.categories.removeIf(item -> item['id'] == params.newCategory['id']);
                            ctx._source.categories.add(params.newCategory);
                        ",
                    'params' => [
                        'newCategory' => $newCategory,
                    ],
                ],
            ],
        ];

        return $this->postTypeRepository->execUpdateByQuery($params);
    }
}
