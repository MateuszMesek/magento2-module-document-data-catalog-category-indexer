<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCatalogCategoryIndexer\Command;

use ArrayIterator;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use MateuszMesek\DocumentDataIndexApi\Command\GetEntityIdsByDimensionsInterface;
use MateuszMesek\DocumentDataIndexApi\DimensionResolverInterface;
use Traversable;

class GetEntityIdsByDimensions implements GetEntityIdsByDimensionsInterface
{
    private DimensionResolverInterface $storeIdResolver;
    private CollectionFactory $collectionFactory;

    public function __construct(
        DimensionResolverInterface $storeIdResolver,
        CollectionFactory $collectionFactory
    )
    {
        $this->storeIdResolver = $storeIdResolver;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute(array $dimensions): Traversable
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId);

        $ids = array_map(
            'intval',
            $collection->getAllIds()
        );

        return new ArrayIterator($ids);
    }
}
