<?php

namespace Generators;
use Faker\Factory;
use Automattic\WooCommerce\Client;

class ProductGenerator
{
    protected $woocommerce;
    protected $faker;

    public function __construct()
    {
        $this->woocommerce = new Client(
            $_ENV['WP_SITE_URL'],
            $_ENV['WC_KEY'],
            $_ENV['WC_SECRET'],
            [
                'version' => $_ENV['WC_API_VERSION'],
                'timeout' => $_ENV['REQUEST_TIMEOUT']
            ]
        );
    }

	public function generate($totalBatch = 1, $batchSize = 100)
    {
        $faker = Factory::create();
        /**
         * Fake Products Generator
         */
        for ($i=1; $i <= $totalBatch; $i++) {
            $data['create'] = [];
            for ($j = 1; $j <= $batchSize; $j++) {
                $price = $faker->numberBetween(50, 200);
                $data['create'][] = [
                    'name' => $faker->sentence(4),
                    'type' => 'simple',
                    'regular_price' => "{$price}",
                    'description' => $faker->sentence(40),
                    'short_description' => $faker->sentence(10),
                    'images' => [
                        ['src' => 'https://picsum.photos/437/437.jpg'],
                        ['src' => 'https://picsum.photos/437/437.jpg'],
                        ['src' => 'https://picsum.photos/437/437.jpg'],
                        ['src' => 'https://picsum.photos/437/437.jpg'],
                        ['src' => 'https://picsum.photos/437/437.jpg']
                    ]
                ];
            }

            $result = $this->woocommerce->post('products/batch', $data);

            echo '<pre>';
            print "Response: " . var_dump($result) . "\r\n";
            print "Imported product. Batch number: " . $i . "\r\n";
        }

        print "Product import completed \r\n";
    }
}