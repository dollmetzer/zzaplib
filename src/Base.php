<?php

/**
 * z z a p l i b   m i n i   f r a m e w o r k
 * ===========================================
 *
 * This library is a mini framework from php web applications
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software 
 * Foundation; either version 3 of the License, or (at your option) any later 
 * version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with 
 * this program; if not, see <http://www.gnu.org/licenses/>. 
 */

namespace dollmetzer\zzaplib;

/**
 * Base class for Application and Api class
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2015 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package zzaplib
 */
class Base {

    /**
     * @var array The configuration of the application 
     */
    public $config;

    /**
     * @var string Name of the module 
     */
    public $moduleName;

    /**
     * @var string The name of the controller 
     */
    public $controllerName;

    /**
     * @var string The name of the action 
     */
    public $actionName;

    /**
     * @var array $params Holds the URL parameters after controller/action/...
     */
    public $params;

    /**
     * @var array Language snippets 
     */
    public $lang;

    /**
     * @var Controller Holds the instance of the Controller 
     */
    public $controller;

    /**
     * @var PDO $dbh Database handle
     */
    public $dbh;

    /**
     * Get a list of installed modules. If modules are set in the configuration,
     * get the list from the configuration. Every module entry must be an array.
     * 
     * 'modules' => array (
     *     'core' => array(
     *         'index',
     *         'account'
     *     )
     *  )
     * 
     * @return array
     */
    protected function getModuleList() {

        if (empty($this->config['modules'])) {
            $list = array();
            if (file_exists(PATH_APP . 'modules/')) {
                $dir = opendir(PATH_APP . 'modules/');
                while ($file = readdir($dir)) {
                    if (!preg_match('/^\./', $file)) {
                        if (is_dir(PATH_APP . 'modules/' . $file)) {
                            $list[] = $file;
                        }
                    }
                }
                closedir($dir);
            }
        } else {
            $list = array_keys($this->config['modules']);
        }

        return $list;
    }

    /**
     * Get a list of available controllers for the module $this->moduleName
     * If modules are set in the configuration, get the list from the
     * configuration. Without configuration entry, get the list from the filesystem.
     * 
     * @return array
     */
    protected function getControllerList() {

        if (!empty($this->config['modules'][$this->moduleName])) {

            $list = $this->config['modules'][$this->moduleName];
        } else {

            $list = array();
            $controllerDir = PATH_APP . 'modules/' . $this->moduleName . '/controllers/';
            $dir = opendir($controllerDir);
            while ($file = readdir($dir)) {
                if (preg_match('/Controller.php$/', $file)) {
                    $list[] = preg_replace('/Controller.php$/', '', $file);
                }
            }
            closedir($dir);
        }
        return $list;
    }

    /**
     * Try to load language snippets and store them in $this->lang
     * The desired language is stored in $_SESSION['user_language'].
     * The name of the file is [language]_[snippet].ini
     * E.g. 'de_account.ini' holds the snippets for the account controller in german.
     * 
     * @param string $_snippet  Name of the snippet - mostly the controller name
     * @param string $_module   Name of the module, if language file shouldn't be for current module
     * @param string $_language two letter code of language, if not to use the user language in the session
     * @return boolean success
     */
    public function loadLanguage($_snippet, $_module = '', $_language = '') {

        if (empty($_language)) {
            $language = $this->session->user_language;
        } else {
            $language = $_language;
        }

        if ($_module == '') {
            $filename = PATH_APP . 'modules/' . $this->moduleName . '/data/' . $language . '_' . $_snippet . '.ini';
        } else {
            $filename = PATH_APP . 'modules/' . $_module . '/data/' . $language . '_' . $_snippet . '.ini';
        }

        if (file_exists($filename)) {
            $lang = parse_ini_file($filename);
            $this->lang = array_merge($this->lang, $lang);
            return true;
        } else {
            error_log('Language File ' . $filename . ' not found');
        }
        return false;
    }

    /**
     * Return a language snippet in the current language
     * 
     * @param string $_snippet Name of the snippet
     * @return string either the snippet, or - if snippet wasn't defined - the name of the snippet, wrapped in ###_ _###
     */
    public function lang($_snippet) {

        if (empty($this->lang[$_snippet])) {
            $text = '###_' . $_snippet . '_###';
        } else {
            $text = $this->lang[$_snippet];
        }

        return $text;
    }

    /**
     * Build a complete URL from a query string
     * 
     * @param string $_path       Query string like controller/action/param_1/param_n 
     * @param array  $_attributes Additional Attributes. Array of key=>value pairs
     * @return string
     */
    public function buildURL($_path, $_attributes = array()) {

        $url = 'http://' . URL_BASE;
        if (URL_REWRITE) {
            $url .= '/' . $_path;
        } else {
            if (!empty($_path))
                $url .= '/index.php?q=' . $_path;
        }

        if (!empty($_attributes)) {
            $addition = array();
            foreach ($_attributes as $key => $val) {
                $addition[] = $key . '=' . urlencode($val);
            }
            $url .= '&' . join('&', $addition);
        }

        return $url;
    }

    /**
     * Returns an array of 2 digit ISO 3166 country codes
     * 
     * @return array
     */
    public function getCountryCodes() {

        return array(
            "AF" => "Afghanistan",
            "AL" => "Albania",
            "DZ" => "Algeria",
            "AS" => "American Samoa",
            "AD" => "Andorra",
            "AG" => "Angola",
            "AI" => "Anguilla",
            "AG" => "Antigua &amp; Barbuda",
            "AR" => "Argentina",
            "AA" => "Armenia",
            "AW" => "Aruba",
            "AU" => "Australia",
            "AT" => "Austria",
            "AZ" => "Azerbaijan",
            "BS" => "Bahamas",
            "BH" => "Bahrain",
            "BD" => "Bangladesh",
            "BB" => "Barbados",
            "BY" => "Belarus",
            "BE" => "Belgium",
            "BZ" => "Belize",
            "BJ" => "Benin",
            "BM" => "Bermuda",
            "BT" => "Bhutan",
            "BO" => "Bolivia",
            "BL" => "Bonaire",
            "BA" => "Bosnia &amp; Herzegovina",
            "BW" => "Botswana",
            "BR" => "Brazil",
            "BC" => "British Indian Ocean Ter",
            "BN" => "Brunei",
            "BG" => "Bulgaria",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodia",
            "CM" => "Cameroon",
            "CA" => "Canada",
            "IC" => "Canary Islands",
            "CV" => "Cape Verde",
            "KY" => "Cayman Islands",
            "CF" => "Central African Republic",
            "TD" => "Chad",
            "CD" => "Channel Islands",
            "CL" => "Chile",
            "CN" => "China",
            "CI" => "Christmas Island",
            "CS" => "Cocos Island",
            "CO" => "Colombia",
            "CC" => "Comoros",
            "CG" => "Congo",
            "CK" => "Cook Islands",
            "CR" => "Costa Rica",
            "CT" => "Cote D'Ivoire",
            "HR" => "Croatia",
            "CU" => "Cuba",
            "CB" => "Curacao",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DK" => "Denmark",
            "DJ" => "Djibouti",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "TM" => "East Timor",
            "EC" => "Ecuador",
            "EG" => "Egypt",
            "SV" => "El Salvador",
            "GQ" => "Equatorial Guinea",
            "ER" => "Eritrea",
            "EE" => "Estonia",
            "ET" => "Ethiopia",
            "FA" => "Falkland Islands",
            "FO" => "Faroe Islands",
            "FJ" => "Fiji",
            "FI" => "Finland",
            "FR" => "France",
            "GF" => "French Guiana",
            "PF" => "French Polynesia",
            "FS" => "French Southern Ter",
            "GA" => "Gabon",
            "GM" => "Gambia",
            "GE" => "Georgia",
            "DE" => "Germany",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GB" => "Great Britain",
            "GR" => "Greece",
            "GL" => "Greenland",
            "GD" => "Grenada",
            "GP" => "Guadeloupe",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GN" => "Guinea",
            "GY" => "Guyana",
            "HT" => "Haiti",
            "HW" => "Hawaii",
            "HN" => "Honduras",
            "HK" => "Hong Kong",
            "HU" => "Hungary",
            "IS" => "Iceland",
            "IN" => "India",
            "ID" => "Indonesia",
            "IA" => "Iran",
            "IQ" => "Iraq",
            "IR" => "Ireland",
            "IM" => "Isle of Man",
            "IL" => "Israel",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JP" => "Japan",
            "JO" => "Jordan",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KI" => "Kiribati",
            "NK" => "Korea North",
            "KS" => "Korea South",
            "KW" => "Kuwait",
            "KG" => "Kyrgyzstan",
            "LA" => "Laos",
            "LV" => "Latvia",
            "LB" => "Lebanon",
            "LS" => "Lesotho",
            "LR" => "Liberia",
            "LY" => "Libya",
            "LI" => "Liechtenstein",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MO" => "Macau",
            "MK" => "Macedonia",
            "MG" => "Madagascar",
            "MY" => "Malaysia",
            "MW" => "Malawi",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malta",
            "MH" => "Marshall Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MU" => "Mauritius",
            "ME" => "Mayotte",
            "MX" => "Mexico",
            "MI" => "Midway Islands",
            "MD" => "Moldova",
            "MC" => "Monaco",
            "MN" => "Mongolia",
            "MS" => "Montserrat",
            "MA" => "Morocco",
            "MZ" => "Mozambique",
            "MM" => "Myanmar",
            "NA" => "Nambia",
            "NU" => "Nauru",
            "NP" => "Nepal",
            "AN" => "Netherland Antilles",
            "NL" => "Netherlands (Holland, Europe)",
            "NV" => "Nevis",
            "NC" => "New Caledonia",
            "NZ" => "New Zealand",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigeria",
            "NW" => "Niue",
            "NF" => "Norfolk Island",
            "NO" => "Norway",
            "OM" => "Oman",
            "PK" => "Pakistan",
            "PW" => "Palau Island",
            "PS" => "Palestine",
            "PA" => "Panama",
            "PG" => "Papua New Guinea",
            "PY" => "Paraguay",
            "PE" => "Peru",
            "PH" => "Philippines",
            "PO" => "Pitcairn Island",
            "PL" => "Poland",
            "PT" => "Portugal",
            "PR" => "Puerto Rico",
            "QA" => "Qatar",
            "ME" => "Republic of Montenegro",
            "RS" => "Republic of Serbia",
            "RE" => "Reunion",
            "RO" => "Romania",
            "RU" => "Russia",
            "RW" => "Rwanda",
            "NT" => "St Barthelemy",
            "EU" => "St Eustatius",
            "HE" => "St Helena",
            "KN" => "St Kitts-Nevis",
            "LC" => "St Lucia",
            "MB" => "St Maarten",
            "PM" => "St Pierre &amp; Miquelon",
            "VC" => "St Vincent &amp; Grenadines",
            "SP" => "Saipan",
            "SO" => "Samoa",
            "AS" => "Samoa American",
            "SM" => "San Marino",
            "ST" => "Sao Tome &amp; Principe",
            "SA" => "Saudi Arabia",
            "SN" => "Senegal",
            "RS" => "Serbia",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapore",
            "SK" => "Slovakia",
            "SI" => "Slovenia",
            "SB" => "Solomon Islands",
            "OI" => "Somalia",
            "ZA" => "South Africa",
            "ES" => "Spain",
            "LK" => "Sri Lanka",
            "SD" => "Sudan",
            "SR" => "Suriname",
            "SZ" => "Swaziland",
            "SE" => "Sweden",
            "CH" => "Switzerland",
            "SY" => "Syria",
            "TA" => "Tahiti",
            "TW" => "Taiwan",
            "TJ" => "Tajikistan",
            "TZ" => "Tanzania",
            "TH" => "Thailand",
            "TG" => "Togo",
            "TK" => "Tokelau",
            "TO" => "Tonga",
            "TT" => "Trinidad &amp; Tobago",
            "TN" => "Tunisia",
            "TR" => "Turkey",
            "TU" => "Turkmenistan",
            "TC" => "Turks &amp; Caicos Is",
            "TV" => "Tuvalu",
            "UG" => "Uganda",
            "UA" => "Ukraine",
            "AE" => "United Arab Emirates",
            "GB" => "United Kingdom",
            "US" => "United States of America",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VU" => "Vanuatu",
            "VS" => "Vatican City State",
            "VE" => "Venezuela",
            "VN" => "Vietnam",
            "VB" => "Virgin Islands (Brit)",
            "VA" => "Virgin Islands (USA)",
            "WK" => "Wake Island",
            "WF" => "Wallis &amp; Futana Is",
            "YE" => "Yemen",
            "ZR" => "Zaire",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe"
        );
    }

}
