CHANGELOG
=========

2018-12-09 Table added $first as marker for the first row in the DB Search results

2018-12-06 DBModel added standard methods for simple search search() and getSearchEntries()

2018-12-06 DBModel added standard methods for paged and sorted lists getList() and getListEntries()

2018-02-03 Form::getValues() fixed notice, if field had no value

2018-01-28 API: request with null session working, added setStatus method, added response class

2018-01-21 config accessible in DBModel

2018-01-11 Fixed codestyle issues

2018-01-10 Fixed docblocks

2018-01-10 DEBUG: Module::buildConfig now activates all protected modules

2017-09-20 removed session from API to become REST compliant

2017-08-22 Reformatted PSR-1/PSR-2 Coding style

2017-06-23 Console commands added

2017-06-20 Form: date validator added

2017-06-19 View::textToShort Text shortener added

2017-06-05 Api functionality updated (to be continued)

2017-06-04 Application: Extended logging. Api: Construction of Controller changed

2017-05-27 View::buildMediaURL added for construction of media URLs

2017-05-27 View::getNavigation now sorts navigation elements

2017-05-25 Controller::isAllowed now supports strings and arrays in $accessGroups

2017-05-16 Application::run checks, if module is active

2017-05-14 Deletion of language and navigation cache files moved to zzablib View class into ~/tmp folder

2017-05-13 Module Class support loading and saving configuartion, setting and getting values

2017-05-11 Started Module Class for managing modules

2017-03-15 View:userInGroup now also checks against arrays

2017-01-20 added Mail Class for sending mails

2017-01-19 added View::getLangaugeCore for collection of language core files from all modules

2017-01-19 added View::getNavigation method for collection navigation items of all modules

2017-01-13 Changed four textindizes

2017-01-07 Form check minlength only, if value is given

2017-01-07 Table added. Session::destroy also inits new session

2017-01-06 Application: changed preAction() to before() and postAction() to after(), as they should not be direct accessible throught the URL

2017-01-06 View: refactored getter and setter for JS and CSS files

2017-01-05 Session::login added

2017-01-03 Started refactoring DBModel

2017-01-03 Started Refactoring of REST API

2017-01-01 Fixed Controller::lang() output. Form constructor changed. Fixed language snippet in Application

2016-12-31 Fixed Controller::lang()

2016-12-28 Fixed tests

2016-12-27 config accessible in view. Fixed some wrapper methods in Controller class.

2016-12-26 Formatting, commenting

2016-12-25 View theme switchable in controller

2016-12-24 Refactoring in progress

2016-12-21 started Version 2.0.0 as rewrite of 0.0.2 and zzaplib2016