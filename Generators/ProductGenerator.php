<?php

namespace Generators;
use Faker\Factory;
use Automattic\WooCommerce\Client;

class ProductGenerator
{
    protected $woocommerce;
    protected $faker;
    protected $imageIds = [];

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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

        $this->fetchImageUrls();
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
                    'name' => str_replace("'", "", $faker->sentence(4)),
                    'type' => 'simple',
                    'regular_price' => "{$price}",
                    'description' => str_replace("'", "", $faker->sentence(40)),
                    'short_description' => str_replace("'", "", $faker->sentence(10)),
                    'images' => [
                        ['id' => $this->imageIds[array_rand($this->imageIds)]],
                        ['id' => $this->imageIds[array_rand($this->imageIds)]]
                    ]
                ];
            }

            $this->woocommerce->post('products/batch', $data);

//            dd($result);

            print "Imported product. Batch number: " . $i . "\r\n";
        }

        print "Product import completed \r\n";
    }

    /*
     * TO GET NONCE: browser console: wpApiSettings : nonce
     * TO GET COOKIE: console > application > cookies > copy key value: wordpress_logged_in_*********
     * Ref: oasisworkflow.com/how-to-authenticate-wp-rest-apis-with-postman
     */
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchImageUrls() {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $_ENV['WP_SITE_URL'] . "wp-json/wp/v2/",
            'timeout'  => $_ENV['REQUEST_TIMEOUT'],
            'headers' => [
                "Accept" => "application/json",
                'X-WP-Nonce' => '3086f530a7',
                'Cookie' => 'wordpress_logged_in_ee0935fc976269648251e63464fddfe4=admin%7C1627039223%7CRPiuZ64aTNtSTOslAXQbeJDllZNmwfL1tpAr9vUNiom%7Cd3e339887922131c5ef6bc44f0d125c2171f1ee9d5c3b136e66d2a658a8eec13'
            ],
            'verify' => false
        ]);
        $response = $client->get( "media", [
            'media_type' => 'image'
        ]);
        $files = json_decode($response->getBody(), true);

        foreach ($files as $image) {
            $this->imageIds[] = $image['id'];
        }

        return $this->imageIds;
    }
}