CHANGELOG
=========
2015-11-14 Added (CRUDE) permission class. NOT READY FRO PRODUCTION YET
2015-10-15 PSR-2 reformatted
2015-10-14 split format of config array in modules
2015-09-14 Translation app/item
2015-09-13 Fixed quicklogin. Forward to target page after login.
2015-08-22 Form::process accept NULL Values as input without notice
2015-08-22 base::log setzt permissions to 0664
2015-08-07 lang() returns placeholder only on !isset() instead of empty() to enable empty language strings
2015-07-25 Added log method to Base Class
2015-07-12 Added a new session parameter user_haspassword
2015-07-06 Moved loadLang() and lang() to Base to enable languages in scripts
2015-06-27 Corrected File upload
2015-06-26 Added capturing for view::render for processing mail templates or enable caching
2015-05-25 Catch controller class not found error in Application class
2014-12-07 Form update checkbox
2014-11-29 View::toDate, toDatetime and toDatetimeShort returning '-' for 0000-00-00 00:00:00 
2014-06-10 DBModel added standard methods create, read, update and delete
2014-06-03 Refactored for use of namespaces 
2014-06-01 Controller::inGroup now working with group ID or group name
2014-03-24 Debug: Application::run message type for error_illegal_parameter is now error 
2014-02-17 Added getCountryCodes for ISO codes
2014-02-11 Application class now an extension of Base class
2014-02-11 Some cosmetic changes in View class
2014-02-07 Solved minor incompatibility with function array_intersect between PHP 5.3 and PHP 5.4
2014-02-04 Added API
2014-02-01 Added group based access control
2014-02-01 Added user groups in session
2014-01-30 Added autoloading for application models
2014-01-26 Initialisation of github repository for zzaplib
2014-01-26 Copied together main classes from various projects to fit into composer package
2014-01-26 Started to port media advertising tool to zzaplib
