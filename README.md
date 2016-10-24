SocialCommunity for Joomla!
============================
( Version 2.2.1 )
----------------------------

Social Community is an extension that adds social features to website based on Joomla! CMS.

##Documentation
You can find documentation on the following pages.

[Documentation and FAQ](http://itprism.com/help/86-social-community-documentation)

[API documentation](http://cdn.itprism.com/api/socialcommunity/index.html)

##Download
You can [download SocialCommunity package](http://itprism.com/free-joomla-extensions/others/open-source-social-network) and additional extensions ( modules and plugins ) from the website of ITPrism.

## About the code in this repository
This repository contains code that you should use to create a package. You will be able to install that package via [Joomla extension manager](https://docs.joomla.org/Help25:Extensions_Extension_Manager_Install).

##How to create a package?
* You should install ANT on your PC.
* Download or clone [Social Community distribution](https://github.com/ITPrism/SocialCommunityDistribution).
* Download or clone the code from this repository.
* Rename the file __build/example.txt__ to __build/antconfig.txt__.
* Edit the file __build/antconfig.txt__. Enter name and version of your package. Enter the folder where the source code is (Social Community distribution). Enter the folder where the source code of the package will be stored (the folder where you have saved this repository).
* Save the file __build/antconfig.txt__.
* Open a console and go in folder __build__.
* Type "__ant__" and click enter. The system will copy all files from distribution to the folder where you are going to build the installable package.

##Contribute
If you would like to contribute to the project you should use Social Community distribution. That repository provides Joomla CMS + Social Community.
You can clone it on your PC and install it on your local host. You should use it as development environment. You should use it to create branches, to add new features, to fix issues and to send pull request.