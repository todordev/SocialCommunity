SocialCommunity for Joomla!
============================
( Version 3.0 )
----------------------------

Social Community is an extension that adds social features to website based on Joomla! CMS.

## Documentation

You can find documentation on the following pages.

[Documentation and FAQ](http://itprism.com/help/86-social-community-documentation)

[API documentation](http://cdn.itprism.com/api/socialcommunity/index.html)

## Download

You can [download SocialCommunity package](http://itprism.com/free-joomla-extensions/others/open-source-social-network) and additional extensions (modules and plugins) from the website of ITPrism.

## About the code in this repository

This repository contains code that you should use to create a package. You will be able to install that package via [Joomla Extension Manager](https://docs.joomla.org/Help25:Extensions_Extension_Manager_Install).
You should install [ANT](http://ant.apache.org/) on your PC to build a package.

## How to create a package?

1. Download or clone this repository.
2. Rename the file __build/antconfig.dist.txt__ to __build/antconfig.txt__.
3. Edit the file __build/antconfig.txt__. Enter name and version of your package. Enter full path to the folder where you downloaded this repository.
4. Open a console and go to folder __build__.
5. Execute `ant`. It will build a package in folder __/packages__.

```bash
ant
```

## Contribute

You should do [pull requests](https://help.github.com/articles/about-pull-requests/) to contribute to this projects.