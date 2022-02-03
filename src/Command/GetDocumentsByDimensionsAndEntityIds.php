<?php declare(strict_types=1);

namespace MateuszMesek\DocumentCatalogCategoryIndexer\Command;

use MateuszMesek\DocumentCatalogCategory\Command\GetDocumentDataByCategoryIdAndStoreId;
use MateuszMesek\DocumentIndexerApi\Command\GetDocumentsByDimensionsAndEntityIdsInterface;
use MateuszMesek\DocumentIndexerApi\DimensionResolverInterface;
use Traversable;

class GetDocumentsByDimensionsAndEntityIds implements GetDocumentsByDimensionsAndEntityIdsInterface
{
    private DimensionResolverInterface $storeIdResolver;
    private GetDocumentDataByCategoryIdAndStoreId $getDocumentDataByCategoryIdAndStoreId;

    public function __construct(
        DimensionResolverInterface $storeIdResolver,
        GetDocumentDataByCategoryIdAndStoreId $getDocumentDataByCategoryIdAndStoreId
    )
    {
        $this->storeIdResolver = $storeIdResolver;
        $this->getDocumentDataByCategoryIdAndStoreId = $getDocumentDataByCategoryIdAndStoreId;
    }

    public function execute(array $dimensions, Traversable $entityIds): Traversable
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        foreach ($entityIds as $entityId) {
            yield $this->getDocumentDataByCategoryIdAndStoreId->execute($entityId, $storeId);
        }
    }
}
