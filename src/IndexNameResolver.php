<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCatalogCategoryIndexer;

use MateuszMesek\DocumentDataIndexerApi\DimensionResolverInterface;
use MateuszMesek\DocumentDataIndexerApi\IndexNameResolverInterface;

class IndexNameResolver implements IndexNameResolverInterface
{
    private DimensionResolverInterface $storeIdResolver;

    public function __construct(
        DimensionResolverInterface $storeIdResolver
    )
    {
        $this->storeIdResolver = $storeIdResolver;
    }

    public function resolve(array $dimensions): string
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        return "category_$storeId";
    }
}
