<?php

namespace Generators;
use Faker\Factory;
use Automattic\WooCommerce\Client;

class CustomerGenerator
{
    protected $woocommerce;
    protected $faker;

    /**
     * CustomerGenerator constructor.
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
    }

    /**
     * @param int $totalBatch
     * @param int $batchSize
     */
    public function generate($totalBatch = 1, $batchSize = 100)
    {
        $faker = Factory::create();

        /**
         * Customer Generator
         */
        for ($i=1; $i <= $totalBatch; $i++) {
            $data['create'] = [];
            for ($j = 1; $j <= $batchSize; $j++) {
                $data['create'][] = [
                    'email' => $faker->email,
                    'first_name' => $fname = $faker->firstName,
                    'last_name' => $lname = $faker->lastName,
                    'username' => $faker->userName,
                    'billing' => [
                        'first_name' => $fname,
                        'last_name' => $lname,
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
                    ]
                ];
            }

            $this->woocommerce->post('customers/batch', $data);

            print "Imported customer. Batch number: " . $i . "\r\n";
        }

        print "Customer import completed \r\n";
    }
}