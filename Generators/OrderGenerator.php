<?php

namespace Generators;
use Faker\Factory;
use Automattic\WooCommerce\Client;

class OrderGenerator
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
		 * Orders Generator
		 */
		// Get All Products
		$products = $this->woocommerce->get('products', ['per_page' => 100]);
		$productIds = [];
		foreach ($products as $product) {
		    array_push($productIds, $product->id);
		}

        for ($i=1; $i <= $totalBatch; $i++) {
            $data['create'] = [];
            for ($j = 1; $j <= $batchSize; $j++) {
                $data['create'][] = [
                    'payment_method' => 'cod',
                    'payment_method_title' => 'Cash on Delivery',
                    'set_paid' => $j % 2 === 0 ? true : false,
                    'billing' => [
                        'first_name' => $fname = $faker->firstName,
                        'last_name' => $lname = $faker->lastName,
                        'address_1' => $address = $faker->address,
                        'address_2' => '',
                        'city' => $city = $faker->city,
                        'state' => $state = $faker->state,
                        'postcode' => $postcode = $faker->postcode,
                        'country' => $country = $faker->countryCode,
                        'email' => $email = $faker->safeEmail,
                        'phone' => $phone = $faker->phoneNumber
                    ],
                    'shipping' => [
                        'first_name' => $fname,
                        'last_name' => $lname,
                        'address_1' => $address,
                        'address_2' => '',
                        'city' => $city,
                        'state' => $state,
                        'postcode' => $postcode,
                        'country' => $country
                    ],
                    'line_items' => [
                        [
                            'product_id' => $faker->randomElement(array_values($productIds)),
                            'quantity' => $faker->numberBetween(1, 10)
                        ]
                    ]
                ];
            }

            $this->woocommerce->post('orders/batch', $data);

            print "Imported order. Batch number: " . $i . "\r\n";
        }

        print "Order import completed \r\n";
    }
}