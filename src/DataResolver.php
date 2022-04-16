<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCatalogCategoryIndexer;

use MateuszMesek\DocumentDataCatalogCategory\Command\GetDocumentDataByCategoryIdAndStoreId;
use MateuszMesek\DocumentDataIndexIndexerApi\DataResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\DimensionResolverInterface;
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
            $data = $this->getDocumentDataByCategoryIdAndStoreId->execute($entityId, $storeId);

            if (empty($data)) {
                return;
            }

            yield $data;
        }
    }
}
