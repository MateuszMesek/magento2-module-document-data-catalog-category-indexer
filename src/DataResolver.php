<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCatalogCategoryIndexer;

use MateuszMesek\DocumentDataCatalogCategory\Command\GetDocumentDataByCategoryIdAndStoreId;
use MateuszMesek\DocumentDataIndexerApi\DataResolverInterface;
use MateuszMesek\DocumentDataIndexerApi\DimensionResolverInterface;
use Traversable;

class DataResolver implements DataResolverInterface
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

    public function resolve(array $dimensions, Traversable $entityIds): Traversable
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        foreach ($entityIds as $entityId) {
            yield $this->getDocumentDataByCategoryIdAndStoreId->execute($entityId, $storeId);
        }
    }
}
