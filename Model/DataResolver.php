<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCatalogCategoryIndexer\Model;

use MateuszMesek\DocumentDataCatalogCategory\Model\Command\GetDocumentDataByCategoryIdAndStoreId;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\DataResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\DimensionResolverInterface;
use Traversable;

class DataResolver implements DataResolverInterface
{
    public function __construct(
        private readonly DimensionResolverInterface            $storeIdResolver,
        private readonly GetDocumentDataByCategoryIdAndStoreId $getDocumentDataByCategoryIdAndStoreId
    )
    {
    }

    public function resolve(array $dimensions, Traversable $entityIds): Traversable
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        foreach ($entityIds as $entityId) {
            $data = $this->getDocumentDataByCategoryIdAndStoreId->execute((int)$entityId, $storeId);

            yield $entityId => $data;
        }
    }
}
