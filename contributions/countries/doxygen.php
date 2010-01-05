<?php
/**
 * @defgroup Countries
 * 
 * A set of countries and their relationships.
 *
 * @section Usage
 *
 * This module offers the following models:
 * 
 * @li countries: All countries that are listed in ISO 3166: http://www.iso.org/iso/english_country_names_and_code_elements
 * @li continents: All continents. Each country is assigend to one continent
 * @li countriesgroups: Groups of countries, currently implemented are European Union, Dependend Territories and Sovereign States.
 * @li countriestranslations: Translation of country names is done using DB, not files. Currently, only German translations are
 *                            maintained (based upon http://www.auswaertiges-amt.de/diplo/de/Infoservice/Terminologie/Staatennamen.pdf)
 *
 * The ID space of countriesgroups is divided into three segments:
 * 
 * \li 1-99: Reserved for use by this module
 * \li 100-999: Reserved for use by other contributions. 
 * \li 1000-: May be used by applications. Autoincrement pointer is set to 1000, so your application can insert groups without setting a fixed ID 
 */
