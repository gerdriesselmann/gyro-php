ALTER TABLE countries 
  ADD COLUMN code3 CHAR(3) DEFAULT NULL AFTER id_continent,
 ADD COLUMN codenum INTEGER UNSIGNED DEFAULT NULL AFTER code3;
	

UPDATE countries SET code3 = 'AFG' WHERE id = 'AF';
UPDATE countries SET code3 = 'ALB' WHERE id = 'AL';
UPDATE countries SET code3 = 'DZA' WHERE id = 'DZ';
UPDATE countries SET code3 = 'ASM' WHERE id = 'AS';
UPDATE countries SET code3 = 'AND' WHERE id = 'AD';
UPDATE countries SET code3 = 'AGO' WHERE id = 'AO';
UPDATE countries SET code3 = 'AIA' WHERE id = 'AI';
UPDATE countries SET code3 = 'ATA' WHERE id = 'AQ';
UPDATE countries SET code3 = 'ATG' WHERE id = 'AG';
UPDATE countries SET code3 = 'ARG' WHERE id = 'AR';
UPDATE countries SET code3 = 'ARM' WHERE id = 'AM';
UPDATE countries SET code3 = 'ABW' WHERE id = 'AW';
UPDATE countries SET code3 = 'AUS' WHERE id = 'AU';
UPDATE countries SET code3 = 'AUT' WHERE id = 'AT';
UPDATE countries SET code3 = 'AZE' WHERE id = 'AZ';
UPDATE countries SET code3 = 'BHS' WHERE id = 'BS';
UPDATE countries SET code3 = 'BHR' WHERE id = 'BH';
UPDATE countries SET code3 = 'BGD' WHERE id = 'BD';
UPDATE countries SET code3 = 'BRB' WHERE id = 'BB';
UPDATE countries SET code3 = 'BLR' WHERE id = 'BY';
UPDATE countries SET code3 = 'BEL' WHERE id = 'BE';
UPDATE countries SET code3 = 'BLZ' WHERE id = 'BZ';
UPDATE countries SET code3 = 'BEN' WHERE id = 'BJ';
UPDATE countries SET code3 = 'BMU' WHERE id = 'BM';
UPDATE countries SET code3 = 'BTN' WHERE id = 'BT';
UPDATE countries SET code3 = 'BOL' WHERE id = 'BO';
UPDATE countries SET code3 = 'BIH' WHERE id = 'BA';
UPDATE countries SET code3 = 'BWA' WHERE id = 'BW';
UPDATE countries SET code3 = 'BVT' WHERE id = 'BV';
UPDATE countries SET code3 = 'BRA' WHERE id = 'BR';
UPDATE countries SET code3 = 'IOT' WHERE id = 'IO';
UPDATE countries SET code3 = 'BRN' WHERE id = 'BN';
UPDATE countries SET code3 = 'BGR' WHERE id = 'BG';
UPDATE countries SET code3 = 'BFA' WHERE id = 'BF';
UPDATE countries SET code3 = 'BDI' WHERE id = 'BI';
UPDATE countries SET code3 = 'KHM' WHERE id = 'KH';
UPDATE countries SET code3 = 'CMR' WHERE id = 'CM';
UPDATE countries SET code3 = 'CAN' WHERE id = 'CA';
UPDATE countries SET code3 = 'CPV' WHERE id = 'CV';
UPDATE countries SET code3 = 'CYM' WHERE id = 'KY';
UPDATE countries SET code3 = 'CAF' WHERE id = 'CF';
UPDATE countries SET code3 = 'TCD' WHERE id = 'TD';
UPDATE countries SET code3 = 'CHL' WHERE id = 'CL';
UPDATE countries SET code3 = 'CHN' WHERE id = 'CN';
UPDATE countries SET code3 = 'CXR' WHERE id = 'CX';
UPDATE countries SET code3 = 'CCK' WHERE id = 'CC';
UPDATE countries SET code3 = 'COL' WHERE id = 'CO';
UPDATE countries SET code3 = 'COM' WHERE id = 'KM';
UPDATE countries SET code3 = 'COG' WHERE id = 'CG';
UPDATE countries SET code3 = 'COD' WHERE id = 'CD';
UPDATE countries SET code3 = 'COK' WHERE id = 'CK';
UPDATE countries SET code3 = 'CRI' WHERE id = 'CR';
UPDATE countries SET code3 = 'CIV' WHERE id = 'CI';
UPDATE countries SET code3 = 'HRV' WHERE id = 'HR';
UPDATE countries SET code3 = 'CUB' WHERE id = 'CU';
UPDATE countries SET code3 = 'CYP' WHERE id = 'CY';
UPDATE countries SET code3 = 'CZE' WHERE id = 'CZ';
UPDATE countries SET code3 = 'DNK' WHERE id = 'DK';
UPDATE countries SET code3 = 'DJI' WHERE id = 'DJ';
UPDATE countries SET code3 = 'DMA' WHERE id = 'DM';
UPDATE countries SET code3 = 'DOM' WHERE id = 'DO';
UPDATE countries SET code3 = 'TMP' WHERE id = 'TP';
UPDATE countries SET code3 = 'ECU' WHERE id = 'EC';
UPDATE countries SET code3 = 'EGY' WHERE id = 'EG';
UPDATE countries SET code3 = 'SLV' WHERE id = 'SV';
UPDATE countries SET code3 = 'GNQ' WHERE id = 'GQ';
UPDATE countries SET code3 = 'ERI' WHERE id = 'ER';
UPDATE countries SET code3 = 'EST' WHERE id = 'EE';
UPDATE countries SET code3 = 'ETH ' WHERE id = 'ET';
UPDATE countries SET code3 = 'FLK' WHERE id = 'FK';
UPDATE countries SET code3 = 'FRO' WHERE id = 'FO';
UPDATE countries SET code3 = 'FJI' WHERE id = 'FJ';
UPDATE countries SET code3 = 'FIN' WHERE id = 'FI';
UPDATE countries SET code3 = 'FRA' WHERE id = 'FR';
UPDATE countries SET code3 = 'FXX' WHERE id = 'FX';
UPDATE countries SET code3 = 'GUF' WHERE id = 'GF';
UPDATE countries SET code3 = 'PYF' WHERE id = 'PF';
UPDATE countries SET code3 = 'ATF' WHERE id = 'TF';
UPDATE countries SET code3 = 'GAB' WHERE id = 'GA';
UPDATE countries SET code3 = 'GMB' WHERE id = 'GM';
UPDATE countries SET code3 = 'GEO' WHERE id = 'GE';
UPDATE countries SET code3 = 'DEU' WHERE id = 'DE';
UPDATE countries SET code3 = 'GHA' WHERE id = 'GH';
UPDATE countries SET code3 = 'GIB' WHERE id = 'GI';
UPDATE countries SET code3 = 'GRC' WHERE id = 'GR';
UPDATE countries SET code3 = 'GRL' WHERE id = 'GL';
UPDATE countries SET code3 = 'GRD' WHERE id = 'GD';
UPDATE countries SET code3 = 'GLP' WHERE id = 'GP';
UPDATE countries SET code3 = 'GUM' WHERE id = 'GU';
UPDATE countries SET code3 = 'GTM' WHERE id = 'GT';
UPDATE countries SET code3 = 'GIN' WHERE id = 'GN';
UPDATE countries SET code3 = 'GNB' WHERE id = 'GW';
UPDATE countries SET code3 = 'GUY' WHERE id = 'GY';
UPDATE countries SET code3 = 'HTI' WHERE id = 'HT';
UPDATE countries SET code3 = 'HMD' WHERE id = 'HM';
UPDATE countries SET code3 = 'VAT' WHERE id = 'VA';
UPDATE countries SET code3 = 'HND' WHERE id = 'HN';
UPDATE countries SET code3 = 'HKG' WHERE id = 'HK';
UPDATE countries SET code3 = 'HUN' WHERE id = 'HU';
UPDATE countries SET code3 = 'ISL' WHERE id = 'IS';
UPDATE countries SET code3 = 'IND' WHERE id = 'IN';
UPDATE countries SET code3 = 'IDN' WHERE id = 'ID';
UPDATE countries SET code3 = 'IRN' WHERE id = 'IR';
UPDATE countries SET code3 = 'IRQ' WHERE id = 'IQ';
UPDATE countries SET code3 = 'IRL' WHERE id = 'IE';
UPDATE countries SET code3 = 'ISR' WHERE id = 'IL';
UPDATE countries SET code3 = 'ITA' WHERE id = 'IT';
UPDATE countries SET code3 = 'JAM' WHERE id = 'JM';
UPDATE countries SET code3 = 'JPN' WHERE id = 'JP';
UPDATE countries SET code3 = 'JOR' WHERE id = 'JO';
UPDATE countries SET code3 = 'KAZ' WHERE id = 'KZ';
UPDATE countries SET code3 = 'KEN' WHERE id = 'KE';
UPDATE countries SET code3 = 'KIR' WHERE id = 'KI';
UPDATE countries SET code3 = 'PRK' WHERE id = 'KP';
UPDATE countries SET code3 = 'KOR' WHERE id = 'KR';
UPDATE countries SET code3 = 'KWT' WHERE id = 'KW';
UPDATE countries SET code3 = 'KGZ' WHERE id = 'KG';
UPDATE countries SET code3 = 'LAO' WHERE id = 'LA';
UPDATE countries SET code3 = 'LVA' WHERE id = 'LV';
UPDATE countries SET code3 = 'LBN' WHERE id = 'LB';
UPDATE countries SET code3 = 'LSO' WHERE id = 'LS';
UPDATE countries SET code3 = 'LBR' WHERE id = 'LR';
UPDATE countries SET code3 = 'LBY' WHERE id = 'LY';
UPDATE countries SET code3 = 'LIE' WHERE id = 'LI';
UPDATE countries SET code3 = 'LTU' WHERE id = 'LT';
UPDATE countries SET code3 = 'LUX' WHERE id = 'LU';
UPDATE countries SET code3 = 'MAC' WHERE id = 'MO';
UPDATE countries SET code3 = 'MKD' WHERE id = 'MK';
UPDATE countries SET code3 = 'MDG' WHERE id = 'MG';
UPDATE countries SET code3 = 'MWI' WHERE id = 'MW';
UPDATE countries SET code3 = 'MYS' WHERE id = 'MY';
UPDATE countries SET code3 = 'MDV' WHERE id = 'MV';
UPDATE countries SET code3 = 'MLI' WHERE id = 'ML';
UPDATE countries SET code3 = 'MLT' WHERE id = 'MT';
UPDATE countries SET code3 = 'MHL' WHERE id = 'MH';
UPDATE countries SET code3 = 'MTQ' WHERE id = 'MQ';
UPDATE countries SET code3 = 'MRT' WHERE id = 'MR';
UPDATE countries SET code3 = 'MUS' WHERE id = 'MU';
UPDATE countries SET code3 = 'MYT' WHERE id = 'YT';
UPDATE countries SET code3 = 'MEX' WHERE id = 'MX';
UPDATE countries SET code3 = 'FSM' WHERE id = 'FM';
UPDATE countries SET code3 = 'MDA' WHERE id = 'MD';
UPDATE countries SET code3 = 'MCO' WHERE id = 'MC';
UPDATE countries SET code3 = 'MNG' WHERE id = 'MN';
UPDATE countries SET code3 = 'MSR' WHERE id = 'MS';
UPDATE countries SET code3 = 'MAR' WHERE id = 'MA';
UPDATE countries SET code3 = 'MOZ' WHERE id = 'MZ';
UPDATE countries SET code3 = 'MMR' WHERE id = 'MM';
UPDATE countries SET code3 = 'NAM' WHERE id = 'NA';
UPDATE countries SET code3 = 'NRU' WHERE id = 'NR';
UPDATE countries SET code3 = 'NPL' WHERE id = 'NP';
UPDATE countries SET code3 = 'NLD' WHERE id = 'NL';
UPDATE countries SET code3 = 'ANT' WHERE id = 'AN';
UPDATE countries SET code3 = 'NCL' WHERE id = 'NC';
UPDATE countries SET code3 = 'NZL' WHERE id = 'NZ';
UPDATE countries SET code3 = 'NIC' WHERE id = 'NI';
UPDATE countries SET code3 = 'NER' WHERE id = 'NE';
UPDATE countries SET code3 = 'NGA' WHERE id = 'NG';
UPDATE countries SET code3 = 'NIU' WHERE id = 'NU';
UPDATE countries SET code3 = 'NFK' WHERE id = 'NF';
UPDATE countries SET code3 = 'MNP' WHERE id = 'MP';
UPDATE countries SET code3 = 'NOR' WHERE id = 'NO';
UPDATE countries SET code3 = 'OMN' WHERE id = 'OM';
UPDATE countries SET code3 = 'PAK' WHERE id = 'PK';
UPDATE countries SET code3 = 'PLW' WHERE id = 'PW';
UPDATE countries SET code3 = 'PAN' WHERE id = 'PA';
UPDATE countries SET code3 = 'PNG' WHERE id = 'PG';
UPDATE countries SET code3 = 'PRY' WHERE id = 'PY';
UPDATE countries SET code3 = 'PER' WHERE id = 'PE';
UPDATE countries SET code3 = 'PHL' WHERE id = 'PH';
UPDATE countries SET code3 = 'PCN' WHERE id = 'PN';
UPDATE countries SET code3 = 'POL' WHERE id = 'PL';
UPDATE countries SET code3 = 'PRT' WHERE id = 'PT';
UPDATE countries SET code3 = 'PRI' WHERE id = 'PR';
UPDATE countries SET code3 = 'QAT' WHERE id = 'QA';
UPDATE countries SET code3 = 'REU' WHERE id = 'RE';
UPDATE countries SET code3 = 'ROM' WHERE id = 'RO';
UPDATE countries SET code3 = 'RUS' WHERE id = 'RU';
UPDATE countries SET code3 = 'RWA' WHERE id = 'RW';
UPDATE countries SET code3 = 'KNA' WHERE id = 'KN';
UPDATE countries SET code3 = 'LCA' WHERE id = 'LC';
UPDATE countries SET code3 = 'VCT' WHERE id = 'VC';
UPDATE countries SET code3 = 'WSM' WHERE id = 'WS';
UPDATE countries SET code3 = 'SMR' WHERE id = 'SM';
UPDATE countries SET code3 = 'STP' WHERE id = 'ST';
UPDATE countries SET code3 = 'SAU' WHERE id = 'SA';
UPDATE countries SET code3 = 'SEN' WHERE id = 'SN';
UPDATE countries SET code3 = 'SYC' WHERE id = 'SC';
UPDATE countries SET code3 = 'SLE' WHERE id = 'SL';
UPDATE countries SET code3 = 'SGP' WHERE id = 'SG';
UPDATE countries SET code3 = 'SVK' WHERE id = 'SK';
UPDATE countries SET code3 = 'SVN' WHERE id = 'SI';
UPDATE countries SET code3 = 'SLB' WHERE id = 'SB';
UPDATE countries SET code3 = 'SOM' WHERE id = 'SO';
UPDATE countries SET code3 = 'ZAF' WHERE id = 'ZA';
UPDATE countries SET code3 = 'SGS' WHERE id = 'GS';
UPDATE countries SET code3 = 'ESP' WHERE id = 'ES';
UPDATE countries SET code3 = 'LKA' WHERE id = 'LK';
UPDATE countries SET code3 = 'SHN' WHERE id = 'SH';
UPDATE countries SET code3 = 'SPM' WHERE id = 'PM';
UPDATE countries SET code3 = 'SDN' WHERE id = 'SD';
UPDATE countries SET code3 = 'SUR' WHERE id = 'SR';
UPDATE countries SET code3 = 'SJM' WHERE id = 'SJ';
UPDATE countries SET code3 = 'SWZ' WHERE id = 'SZ';
UPDATE countries SET code3 = 'SWE' WHERE id = 'SE';
UPDATE countries SET code3 = 'CHE' WHERE id = 'CH';
UPDATE countries SET code3 = 'SYR' WHERE id = 'SY';
UPDATE countries SET code3 = 'TWN' WHERE id = 'TW';
UPDATE countries SET code3 = 'TJK' WHERE id = 'TJ';
UPDATE countries SET code3 = 'TZA' WHERE id = 'TZ';
UPDATE countries SET code3 = 'THA' WHERE id = 'TH';
UPDATE countries SET code3 = 'TGO' WHERE id = 'TG';
UPDATE countries SET code3 = 'TKL' WHERE id = 'TK';
UPDATE countries SET code3 = 'TON' WHERE id = 'TO';
UPDATE countries SET code3 = 'TTO' WHERE id = 'TT';
UPDATE countries SET code3 = 'TUN' WHERE id = 'TN';
UPDATE countries SET code3 = 'TUR' WHERE id = 'TR';
UPDATE countries SET code3 = 'TKM' WHERE id = 'TM';
UPDATE countries SET code3 = 'TCA' WHERE id = 'TC';
UPDATE countries SET code3 = 'TUV' WHERE id = 'TV';
UPDATE countries SET code3 = 'UGA' WHERE id = 'UG';
UPDATE countries SET code3 = 'UKR' WHERE id = 'UA';
UPDATE countries SET code3 = 'ARE' WHERE id = 'AE';
UPDATE countries SET code3 = 'GBR' WHERE id = 'GB';
UPDATE countries SET code3 = 'USA' WHERE id = 'US';
UPDATE countries SET code3 = 'UMI' WHERE id = 'UM';
UPDATE countries SET code3 = 'URY' WHERE id = 'UY';
UPDATE countries SET code3 = 'UZB' WHERE id = 'UZ';
UPDATE countries SET code3 = 'VUT' WHERE id = 'VU';
UPDATE countries SET code3 = 'VEN' WHERE id = 'VE';
UPDATE countries SET code3 = 'VNM' WHERE id = 'VN';
UPDATE countries SET code3 = 'VGB' WHERE id = 'VG';
UPDATE countries SET code3 = 'VIR' WHERE id = 'VI';
UPDATE countries SET code3 = 'WLF' WHERE id = 'WF';
UPDATE countries SET code3 = 'ESH' WHERE id = 'EH';
UPDATE countries SET code3 = 'YEM' WHERE id = 'YE';
UPDATE countries SET code3 = 'YUG' WHERE id = 'YU';
UPDATE countries SET code3 = 'ZMB' WHERE id = 'ZM';
UPDATE countries SET code3 = 'ZWE ' WHERE id = 'ZW';


UPDATE countries SET codenum = '4' WHERE id = 'AF';
UPDATE countries SET codenum = '8' WHERE id = 'AL';
UPDATE countries SET codenum = '12' WHERE id = 'DZ';
UPDATE countries SET codenum = '16' WHERE id = 'AS';
UPDATE countries SET codenum = '20' WHERE id = 'AD';
UPDATE countries SET codenum = '24' WHERE id = 'AO';
UPDATE countries SET codenum = '660' WHERE id = 'AI';
UPDATE countries SET codenum = '10' WHERE id = 'AQ';
UPDATE countries SET codenum = '28' WHERE id = 'AG';
UPDATE countries SET codenum = '32' WHERE id = 'AR';
UPDATE countries SET codenum = '51' WHERE id = 'AM';
UPDATE countries SET codenum = '533' WHERE id = 'AW';
UPDATE countries SET codenum = '36' WHERE id = 'AU';
UPDATE countries SET codenum = '40' WHERE id = 'AT';
UPDATE countries SET codenum = '31' WHERE id = 'AZ';
UPDATE countries SET codenum = '44' WHERE id = 'BS';
UPDATE countries SET codenum = '48' WHERE id = 'BH';
UPDATE countries SET codenum = '50' WHERE id = 'BD';
UPDATE countries SET codenum = '52' WHERE id = 'BB';
UPDATE countries SET codenum = '112' WHERE id = 'BY';
UPDATE countries SET codenum = '56' WHERE id = 'BE';
UPDATE countries SET codenum = '84' WHERE id = 'BZ';
UPDATE countries SET codenum = '204' WHERE id = 'BJ';
UPDATE countries SET codenum = '60' WHERE id = 'BM';
UPDATE countries SET codenum = '64' WHERE id = 'BT';
UPDATE countries SET codenum = '68' WHERE id = 'BO';
UPDATE countries SET codenum = '70' WHERE id = 'BA';
UPDATE countries SET codenum = '72' WHERE id = 'BW';
UPDATE countries SET codenum = '74' WHERE id = 'BV';
UPDATE countries SET codenum = '76' WHERE id = 'BR';
UPDATE countries SET codenum = '086 ' WHERE id = 'IO';
UPDATE countries SET codenum = '96' WHERE id = 'BN';
UPDATE countries SET codenum = '100' WHERE id = 'BG';
UPDATE countries SET codenum = '854' WHERE id = 'BF';
UPDATE countries SET codenum = '108' WHERE id = 'BI';
UPDATE countries SET codenum = '116' WHERE id = 'KH';
UPDATE countries SET codenum = '120' WHERE id = 'CM';
UPDATE countries SET codenum = '124' WHERE id = 'CA';
UPDATE countries SET codenum = '132' WHERE id = 'CV';
UPDATE countries SET codenum = '136' WHERE id = 'KY';
UPDATE countries SET codenum = '140' WHERE id = 'CF';
UPDATE countries SET codenum = '148' WHERE id = 'TD';
UPDATE countries SET codenum = '152' WHERE id = 'CL';
UPDATE countries SET codenum = '156' WHERE id = 'CN';
UPDATE countries SET codenum = '162' WHERE id = 'CX';
UPDATE countries SET codenum = '166' WHERE id = 'CC';
UPDATE countries SET codenum = '170' WHERE id = 'CO';
UPDATE countries SET codenum = '174' WHERE id = 'KM';
UPDATE countries SET codenum = '178' WHERE id = 'CG';
UPDATE countries SET codenum = '180' WHERE id = 'CD';
UPDATE countries SET codenum = '184' WHERE id = 'CK';
UPDATE countries SET codenum = '188' WHERE id = 'CR';
UPDATE countries SET codenum = '384' WHERE id = 'CI';
UPDATE countries SET codenum = '191' WHERE id = 'HR';
UPDATE countries SET codenum = '192' WHERE id = 'CU';
UPDATE countries SET codenum = '196' WHERE id = 'CY';
UPDATE countries SET codenum = '203' WHERE id = 'CZ';
UPDATE countries SET codenum = '208' WHERE id = 'DK';
UPDATE countries SET codenum = '262' WHERE id = 'DJ';
UPDATE countries SET codenum = '212' WHERE id = 'DM';
UPDATE countries SET codenum = '214' WHERE id = 'DO';
UPDATE countries SET codenum = '626' WHERE id = 'TP';
UPDATE countries SET codenum = '218' WHERE id = 'EC';
UPDATE countries SET codenum = '818' WHERE id = 'EG';
UPDATE countries SET codenum = '222' WHERE id = 'SV';
UPDATE countries SET codenum = '226' WHERE id = 'GQ';
UPDATE countries SET codenum = '232' WHERE id = 'ER';
UPDATE countries SET codenum = '233' WHERE id = 'EE';
UPDATE countries SET codenum = '231' WHERE id = 'ET';
UPDATE countries SET codenum = '238' WHERE id = 'FK';
UPDATE countries SET codenum = '234' WHERE id = 'FO';
UPDATE countries SET codenum = '242' WHERE id = 'FJ';
UPDATE countries SET codenum = '246' WHERE id = 'FI';
UPDATE countries SET codenum = '250' WHERE id = 'FR';
UPDATE countries SET codenum = '249' WHERE id = 'FX';
UPDATE countries SET codenum = '254' WHERE id = 'GF';
UPDATE countries SET codenum = '258' WHERE id = 'PF';
UPDATE countries SET codenum = '260' WHERE id = 'TF';
UPDATE countries SET codenum = '266' WHERE id = 'GA';
UPDATE countries SET codenum = '270' WHERE id = 'GM';
UPDATE countries SET codenum = '268' WHERE id = 'GE';
UPDATE countries SET codenum = '276' WHERE id = 'DE';
UPDATE countries SET codenum = '288' WHERE id = 'GH';
UPDATE countries SET codenum = '292' WHERE id = 'GI';
UPDATE countries SET codenum = '300' WHERE id = 'GR';
UPDATE countries SET codenum = '304' WHERE id = 'GL';
UPDATE countries SET codenum = '308' WHERE id = 'GD';
UPDATE countries SET codenum = '312' WHERE id = 'GP';
UPDATE countries SET codenum = '316' WHERE id = 'GU';
UPDATE countries SET codenum = '320' WHERE id = 'GT';
UPDATE countries SET codenum = '324' WHERE id = 'GN';
UPDATE countries SET codenum = '624' WHERE id = 'GW';
UPDATE countries SET codenum = '328' WHERE id = 'GY';
UPDATE countries SET codenum = '332' WHERE id = 'HT';
UPDATE countries SET codenum = '334' WHERE id = 'HM';
UPDATE countries SET codenum = '336' WHERE id = 'VA';
UPDATE countries SET codenum = '340' WHERE id = 'HN';
UPDATE countries SET codenum = '344' WHERE id = 'HK';
UPDATE countries SET codenum = '348' WHERE id = 'HU';
UPDATE countries SET codenum = '352' WHERE id = 'IS';
UPDATE countries SET codenum = '356' WHERE id = 'IN';
UPDATE countries SET codenum = '360' WHERE id = 'ID';
UPDATE countries SET codenum = '364' WHERE id = 'IR';
UPDATE countries SET codenum = '368' WHERE id = 'IQ';
UPDATE countries SET codenum = '372' WHERE id = 'IE';
UPDATE countries SET codenum = '376' WHERE id = 'IL';
UPDATE countries SET codenum = '380' WHERE id = 'IT';
UPDATE countries SET codenum = '388' WHERE id = 'JM';
UPDATE countries SET codenum = '392' WHERE id = 'JP';
UPDATE countries SET codenum = '400' WHERE id = 'JO';
UPDATE countries SET codenum = '398' WHERE id = 'KZ';
UPDATE countries SET codenum = '404' WHERE id = 'KE';
UPDATE countries SET codenum = '296' WHERE id = 'KI';
UPDATE countries SET codenum = '408' WHERE id = 'KP';
UPDATE countries SET codenum = '410' WHERE id = 'KR';
UPDATE countries SET codenum = '414' WHERE id = 'KW';
UPDATE countries SET codenum = '417' WHERE id = 'KG';
UPDATE countries SET codenum = '418' WHERE id = 'LA';
UPDATE countries SET codenum = '428' WHERE id = 'LV';
UPDATE countries SET codenum = '422' WHERE id = 'LB';
UPDATE countries SET codenum = '426' WHERE id = 'LS';
UPDATE countries SET codenum = '430' WHERE id = 'LR';
UPDATE countries SET codenum = '434' WHERE id = 'LY';
UPDATE countries SET codenum = '438' WHERE id = 'LI';
UPDATE countries SET codenum = '440' WHERE id = 'LT';
UPDATE countries SET codenum = '442' WHERE id = 'LU';
UPDATE countries SET codenum = '446' WHERE id = 'MO';
UPDATE countries SET codenum = '807' WHERE id = 'MK';
UPDATE countries SET codenum = '450' WHERE id = 'MG';
UPDATE countries SET codenum = '454' WHERE id = 'MW';
UPDATE countries SET codenum = '458' WHERE id = 'MY';
UPDATE countries SET codenum = '462' WHERE id = 'MV';
UPDATE countries SET codenum = '466' WHERE id = 'ML';
UPDATE countries SET codenum = '470' WHERE id = 'MT';
UPDATE countries SET codenum = '584' WHERE id = 'MH';
UPDATE countries SET codenum = '474' WHERE id = 'MQ';
UPDATE countries SET codenum = '478' WHERE id = 'MR';
UPDATE countries SET codenum = '480' WHERE id = 'MU';
UPDATE countries SET codenum = '175' WHERE id = 'YT';
UPDATE countries SET codenum = '484' WHERE id = 'MX';
UPDATE countries SET codenum = '583' WHERE id = 'FM';
UPDATE countries SET codenum = '498' WHERE id = 'MD';
UPDATE countries SET codenum = '492' WHERE id = 'MC';
UPDATE countries SET codenum = '496' WHERE id = 'MN';
UPDATE countries SET codenum = '500' WHERE id = 'MS';
UPDATE countries SET codenum = '504' WHERE id = 'MA';
UPDATE countries SET codenum = '508' WHERE id = 'MZ';
UPDATE countries SET codenum = '104' WHERE id = 'MM';
UPDATE countries SET codenum = '516' WHERE id = 'NA';
UPDATE countries SET codenum = '520' WHERE id = 'NR';
UPDATE countries SET codenum = '524' WHERE id = 'NP';
UPDATE countries SET codenum = '528' WHERE id = 'NL';
UPDATE countries SET codenum = '530' WHERE id = 'AN';
UPDATE countries SET codenum = '540' WHERE id = 'NC';
UPDATE countries SET codenum = '554' WHERE id = 'NZ';
UPDATE countries SET codenum = '558' WHERE id = 'NI';
UPDATE countries SET codenum = '562' WHERE id = 'NE';
UPDATE countries SET codenum = '566' WHERE id = 'NG';
UPDATE countries SET codenum = '570' WHERE id = 'NU';
UPDATE countries SET codenum = '574' WHERE id = 'NF';
UPDATE countries SET codenum = '580' WHERE id = 'MP';
UPDATE countries SET codenum = '578' WHERE id = 'NO';
UPDATE countries SET codenum = '512' WHERE id = 'OM';
UPDATE countries SET codenum = '586' WHERE id = 'PK';
UPDATE countries SET codenum = '585' WHERE id = 'PW';
UPDATE countries SET codenum = '591' WHERE id = 'PA';
UPDATE countries SET codenum = '598' WHERE id = 'PG';
UPDATE countries SET codenum = '600' WHERE id = 'PY';
UPDATE countries SET codenum = '604' WHERE id = 'PE';
UPDATE countries SET codenum = '608' WHERE id = 'PH';
UPDATE countries SET codenum = '612' WHERE id = 'PN';
UPDATE countries SET codenum = '616' WHERE id = 'PL';
UPDATE countries SET codenum = '620' WHERE id = 'PT';
UPDATE countries SET codenum = '630' WHERE id = 'PR';
UPDATE countries SET codenum = '634' WHERE id = 'QA';
UPDATE countries SET codenum = '638' WHERE id = 'RE';
UPDATE countries SET codenum = '642' WHERE id = 'RO';
UPDATE countries SET codenum = '643' WHERE id = 'RU';
UPDATE countries SET codenum = '646' WHERE id = 'RW';
UPDATE countries SET codenum = '659' WHERE id = 'KN';
UPDATE countries SET codenum = '662' WHERE id = 'LC';
UPDATE countries SET codenum = '670' WHERE id = 'VC';
UPDATE countries SET codenum = '882' WHERE id = 'WS';
UPDATE countries SET codenum = '674' WHERE id = 'SM';
UPDATE countries SET codenum = '678' WHERE id = 'ST';
UPDATE countries SET codenum = '682' WHERE id = 'SA';
UPDATE countries SET codenum = '686' WHERE id = 'SN';
UPDATE countries SET codenum = '690' WHERE id = 'SC';
UPDATE countries SET codenum = '694' WHERE id = 'SL';
UPDATE countries SET codenum = '702' WHERE id = 'SG';
UPDATE countries SET codenum = '703' WHERE id = 'SK';
UPDATE countries SET codenum = '705' WHERE id = 'SI';
UPDATE countries SET codenum = '90' WHERE id = 'SB';
UPDATE countries SET codenum = '706' WHERE id = 'SO';
UPDATE countries SET codenum = '710' WHERE id = 'ZA';
UPDATE countries SET codenum = '239' WHERE id = 'GS';
UPDATE countries SET codenum = '724' WHERE id = 'ES';
UPDATE countries SET codenum = '144' WHERE id = 'LK';
UPDATE countries SET codenum = '654' WHERE id = 'SH';
UPDATE countries SET codenum = '666' WHERE id = 'PM';
UPDATE countries SET codenum = '736' WHERE id = 'SD';
UPDATE countries SET codenum = '740' WHERE id = 'SR';
UPDATE countries SET codenum = '744' WHERE id = 'SJ';
UPDATE countries SET codenum = '748' WHERE id = 'SZ';
UPDATE countries SET codenum = '752' WHERE id = 'SE';
UPDATE countries SET codenum = '756' WHERE id = 'CH';
UPDATE countries SET codenum = '760' WHERE id = 'SY';
UPDATE countries SET codenum = '158' WHERE id = 'TW';
UPDATE countries SET codenum = '762' WHERE id = 'TJ';
UPDATE countries SET codenum = '834' WHERE id = 'TZ';
UPDATE countries SET codenum = '764' WHERE id = 'TH';
UPDATE countries SET codenum = '768' WHERE id = 'TG';
UPDATE countries SET codenum = '772' WHERE id = 'TK';
UPDATE countries SET codenum = '776' WHERE id = 'TO';
UPDATE countries SET codenum = '780' WHERE id = 'TT';
UPDATE countries SET codenum = '788' WHERE id = 'TN';
UPDATE countries SET codenum = '792' WHERE id = 'TR';
UPDATE countries SET codenum = '795' WHERE id = 'TM';
UPDATE countries SET codenum = '796' WHERE id = 'TC';
UPDATE countries SET codenum = '798' WHERE id = 'TV';
UPDATE countries SET codenum = '800' WHERE id = 'UG';
UPDATE countries SET codenum = '804' WHERE id = 'UA';
UPDATE countries SET codenum = '784' WHERE id = 'AE';
UPDATE countries SET codenum = '826' WHERE id = 'GB';
UPDATE countries SET codenum = '840' WHERE id = 'US';
UPDATE countries SET codenum = '581' WHERE id = 'UM';
UPDATE countries SET codenum = '858' WHERE id = 'UY';
UPDATE countries SET codenum = '860' WHERE id = 'UZ';
UPDATE countries SET codenum = '548' WHERE id = 'VU';
UPDATE countries SET codenum = '862' WHERE id = 'VE';
UPDATE countries SET codenum = '704' WHERE id = 'VN';
UPDATE countries SET codenum = '92' WHERE id = 'VG';
UPDATE countries SET codenum = '850' WHERE id = 'VI';
UPDATE countries SET codenum = '876' WHERE id = 'WF';
UPDATE countries SET codenum = '732' WHERE id = 'EH';
UPDATE countries SET codenum = '887' WHERE id = 'YE';
UPDATE countries SET codenum = '891' WHERE id = 'YU';
UPDATE countries SET codenum = '894' WHERE id = 'ZM';
UPDATE countries SET codenum = '716 ' WHERE id = 'ZW';

UPDATE countries SET code3 = 'ALA', codenum = 248 WHERE id = 'AX';
UPDATE countries SET code3 = 'MNE', codenum = 499 WHERE id = 'ME';
UPDATE countries SET code3 = 'TLS', codenum = 626 WHERE id = 'TL';
UPDATE countries SET code3 = 'SRB', codenum = 688 WHERE id = 'RS';
UPDATE countries SET code3 = 'PSE', codenum = 275 WHERE id = 'PS';

ALTER TABLE `countries` 
 MODIFY COLUMN `id_continent` CHAR(2) NOT NULL,
 MODIFY COLUMN `code3` CHAR(3) NOT NULL,
 MODIFY COLUMN `codenum` INT(10) UNSIGNED NOT NULL;