Coding Style
------------
Zzaplib is written in the PSR-1/PSR-2 coding style without any additions or modifications.
It's a widely accepted style, supported by most current IDE and codecheckers.

If you want to contribute code to the project, please ensure beforehand that your code is in
PSR-1/PSR-2 Style.

Naming Convention
-----------------
**Directories**

Directory names are camel case with a first lowercase letter. Directories should group classes.
E.g. all models should be in the model directory.

Use singular, not plural (e.g. 'model' instead of 'models').

**Classnames**

All Classnames are camel case with a first uppercase letter. Underscores are not allowed.
Classnames should be a noun in one word, telling what the obejct IS.

**Methodnames**

Method names are camel case with a first lowercase letter. The should be a verb, describing,
what the method DOES. Methods that return a boolean value should start with 'has' or 'is'.