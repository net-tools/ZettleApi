<?php

declare(strict_types=1);

namespace LauLamanApps\iZettleApi\Client\Product;

use DateTime;
use LauLamanApps\iZettleApi\API\Product\Product;
use LauLamanApps\iZettleApi\API\Product\ProductCollection;
use LauLamanApps\iZettleApi\Client\ImageParser;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;

final class ProductParser
{
    public static function createFromResponse(ResponseInterface $response): array
    {
        $products = [];
        $data = json_decode($response->getBody()->getContents(), true);

        foreach ($data as $purchase) {
            $products[] = self::parse($purchase);
        }

        return $products;
    }

    public static function parseArray(array $products): ProductCollection
    {
        $productCollection = new ProductCollection();

        foreach ($products as $product) {
            $productCollection->add(self::parse($product));
        }

        return $productCollection;
    }

    private static function parse(array $product): Product
    {
        return Product::create(
            Uuid::fromString($product['uuid']),
            CategoryParser::parseArray($product['categories']),
            $product['name'],
            $product['description'],
            ImageParser::parseArray($product['imageLookupKeys']),
            VariantParser::parseArray($product['variants']),
            $product['externalReference'],
            $product['etag'],
            new DateTime($product['updated']),
            Uuid::fromString($product['updatedBy']),
            new DateTime($product['created']),
            (float) $product['vatPercentage']
        );
    }
}