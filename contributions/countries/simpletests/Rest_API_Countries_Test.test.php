<?php

/**
 * Created by PhpStorm.
 * User: sebastian
 * Date: 20.03.17
 * Time: 17:03
 */
Load::models('countries');

class Rest_API_Countries_Test extends GyroUnitTestCase
{
    private $countries_from_api;

    public function __construct()
    {
        $this->countries_from_api = json_decode(file_get_contents('https://restcountries.eu/rest/v2/all'), true);
    }

    public function test_values_of_countries_api()
    {
        $this->assertFalse(empty($this->countries_from_api));
    }

    public function test_every_country_in_rest_world_api_exists_in_gyro()
    {

        foreach ($this->countries_from_api as $country) {
            /* @var $dao_country DAOCountries */
            $this->assertFalse(empty(Countries::get($country['alpha2Code'])));
        }
    }

    public function test_every_country_in_gyro_exists_in_rest_world_api()
    {
        if ($this->countries_from_api) {
            $dao_countries = Countries::create_adapter();
            $dao_countries->find();
            /* @var $dao_country DAOCountries */
            while ($dao_countries->fetch()) {
                $alpha2Code = $dao_countries->id;

                if (!$dao_countries->is_deprecated) {
                    $rest_country = $this->get_country_for_alpha2code($alpha2Code);
                    $this->assertFalse(empty($rest_country), "Country $alpha2Code not found in REST Api");
                    $this->assertEqual($rest_country['alpha2Code'], $alpha2Code);
                }
            }
        }
    }

    public function test_is_capital_of_city_matching_to_country()
    {
        if ($this->countries_from_api) {
            $capitals_to_ignore = array(
                'Saint John\'s', # St. John's
                'Papeetē', # Papeete
                'Rome', # Vatican
                'City of Victoria', # Victoria City
                'Reykjavík', # Reykjavik
                '', # The restworldapi doesn't have capital for Macao (It's Macau)
                'Chișinău', # Chişinău
                'Nouméa', # Noumea
                'City of San Marino', # San Marino
                'Washington, D.C.' # Washington
            );

            foreach ($this->countries_from_api as $country) {
                $dao_country = Countries::get($country['alpha2Code']);

                if (!in_array($country['capital'], $capitals_to_ignore)) {
                    $this->assertEqual(
                        $dao_country->capital,
                        $country['capital'],
                        sprintf(
                            'The capital "%s" does not match to "%s" for country "%s" ("%s")',
                            $dao_country->capital,
                            $country['capital'],
                            $country['alpha2Code'],
                            $country['name']
                        )
                    );
                }
            }
        }
    }

    public function test_is_alpha3Code_matching_to_country()
    {
        if ($this->countries_from_api) {
            foreach ($this->countries_from_api as $country) {
                $dao_country = Countries::get($country['alpha2Code']);

                if ($country['alpha3Code'] !== 'KOS') {
                    $this->assertEqual(
                        $dao_country->code3,
                        $country['alpha3Code'],
                        sprintf(
                            'The ISO Alpha-3 code "%s" is not equal to "%s" for country "%s" ("%s")',
                            $dao_country->code3,
                            $country['alpha3Code'],
                            $country['alpha2Code'],
                            $country['name']
                        )
                    );
                }
            }
        }
    }

    public function test_is_area_of_country_matching_to_country()
    {
        $MAX_DIFFERENCE_PERCENT = 1;

        if ($this->countries_from_api) {
            foreach ($this->countries_from_api as $country) {
                $dao_country = Countries::get($country['alpha2Code']);
                $score = $this->calculation_of_difference_in_area($dao_country->area, $country['area']);

                $this->assertTrue(
                    $score <= $MAX_DIFFERENCE_PERCENT,
                    sprintf(
                        'The area "%s" of country "%s" ("%s") is not equal to "%s". Difference: "%f"',
                        $dao_country->area,
                        $dao_country->id,
                        $country['name'],
                        $country['area'],
                        $score
                    )
                );
            }
        }
    }

    private function calculation_of_difference_in_area($dao_country_area, $country_area)
    {
        if ($country_area) {
            $relation = $dao_country_area / $country_area;
            $percent = round($relation * 100, 1);
        } else {
            $percent = 100.0;
        }

        return abs(100.0 - $percent);
    }

    /**
     * @param $alpha2Code
     * @return array|false
     */
    private function get_country_for_alpha2code($alpha2Code)
    {
        $ret = false;
        foreach($this->countries_from_api as $c)  {
            if ($c['alpha2Code'] === $alpha2Code) {
                $ret = $c;
                break;
            }
        }

        return $ret;
    }

    /*
    * Reason of comment:
    * * Some of countries names was not nice and obvious for people like Holy See (VA) for Vatican.
    public function test_is_name_of_country_matching()
    {
        if ($this->countries_from_api) {
            foreach ($this->countries_from_api as $country) {
                $dao_country = Countries::get($country['alpha2Code']);
                $this->assertEqual(
                    $dao_country->name,
                    $country['name'],
                    sprintf(
                        'The default name of country "%s" does not match for "%s" ("%s")',
                        $dao_country->name,
                        $country['name'],
                        $country['alpha2Code']
                    )
                );
            }
        }
    }
    */

    /*
    * Reason of comment:
    * * The people are born every day, because of that testing it have any sense.
    public function test_is_population_of_country_matching_to_country()
    {
        if ($this->countries_from_api) {
            foreach ($this->countries_from_api as $country) {
                $dao_country = Countries::get($country['alpha2Code']);

                $this->assertEqual(
                    $dao_country->population,
                    $country['population'],
                    sprintf(
                        'The summary of population "%s" is not equal to "%s"',
                        $dao_country->population,
                        $country['population']
                    )
                );
            }
        }
    }
    */
}