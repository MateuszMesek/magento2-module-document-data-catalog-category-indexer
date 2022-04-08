<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCatalogCategoryIndexer\Command;

use MateuszMesek\DocumentDataCatalogCategory\Command\GetDocumentDataByCategoryIdAndStoreId;
use MateuszMesek\DocumentDataIndexApi\Command\GetDocumentsByDimensionsAndEntityIdsInterface;
use MateuszMesek\DocumentDataIndexApi\DimensionResolverInterface;
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
