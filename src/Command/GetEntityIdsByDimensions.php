<?php declare(strict_types=1);

namespace MateuszMesek\DocumentCatalogCategoryIndexer\Command;

use ArrayIterator;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use MateuszMesek\DocumentIndexerApi\Command\GetEntityIdsByDimensionsInterface;
use MateuszMesek\DocumentIndexerApi\DimensionResolverInterface;
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

        return new ArrayIterator($collection->getAllIds());
    }
}
