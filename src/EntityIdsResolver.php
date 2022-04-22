<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCatalogCategoryIndexer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\DB\Query\Generator as QueryGenerator;
use Magento\Store\Model\StoreManagerInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\DimensionResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\EntityIdsResolverInterface;
use PDO;
use Traversable;

class EntityIdsResolver implements EntityIdsResolverInterface
{
    private DimensionResolverInterface $storeIdResolver;
    private StoreManagerInterface $storeManager;
    private CategoryRepositoryInterface $categoryRepository;
    private CollectionFactory $collectionFactory;
    private QueryGenerator $queryGenerator;

    public function __construct(
        DimensionResolverInterface $storeIdResolver,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $collectionFactory,
        QueryGenerator $queryGenerator
    )
    {
        $this->storeIdResolver = $storeIdResolver;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->collectionFactory = $collectionFactory;
        $this->queryGenerator = $queryGenerator;
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
