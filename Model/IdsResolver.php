<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCatalogCategoryIndexer\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\DB\Query\Generator as QueryGenerator;
use Magento\Store\Model\StoreManagerInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\DimensionResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\IdsResolverInterface;
use PDO;
use Traversable;

class IdsResolver implements IdsResolverInterface
{
    public function __construct(
        private readonly DimensionResolverInterface  $storeIdResolver,
        private readonly StoreManagerInterface       $storeManager,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CollectionFactory           $collectionFactory,
        private readonly QueryGenerator              $queryGenerator
    )
    {
    }

    public function resolve(array $dimensions): Traversable
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore($storeId);

        $rootCategory = $this->categoryRepository->get((int)$store->getRootCategoryId(), $storeId);

        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addPathFilter($rootCategory->getPath());

        $queries = $this->queryGenerator->generate(
            'entity_id',
            $collection->getAllIdsSql()
        );

        $connection = $collection->getConnection();

        foreach ($queries as $query) {
            $ids = $connection->query($query)->fetchAll(PDO::FETCH_COLUMN);

            yield from array_map(
                'intval',
                $ids
            );
        }
    }
}
