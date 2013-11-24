[Smarty][@smarty] PHP Template Engine
=====================================

[@noiselabs]:       https://github.com/noiselabs            "NoiseLabs.org"
[@ppi]:             http://www.ppi.io/                      "PPI Framework"
[@smarty]:          http://www.smarty.net/                  "The compiling PHP template engine"
[@symfony]:         http://www.symfony.com/                 "High Performance PHP Framework for Web Development"

About this repository
---------------------

This repository was created from the [Smarty svn sources](https://smarty-php.googlecode.com/svn) using the [svn2git](https://github.com/nirvdrum/svn2git) tool:

    $ svn2git https://smarty-php.googlecode.com/svn -v

and is updated at each Smarty release with:

    $ svn2git --rebase -v

On top of the Smarty code we add some patches to make it work better with the [SmartyBundle](https://smartybundle.readthedocs.org/) library.

What is Smarty?
---------------

Smarty is a template engine for PHP, facilitating the separation of presentation (HTML/CSS) from application logic. This implies that PHP code is application logic, and is separated from the presentation.

Some of Smarty's features:

* It is extremely fast.
* It is efficient since the PHP parser does the dirty work.
* No template parsing overhead, only compiles once.
* It is smart about recompiling only the template files that have changed.
* You can easily create your own custom functions and variable modifiers, so the template language is extremely extensible.
* Configurable template `{delimiter}` tag syntax, so you can use `{$foo}, {{$foo}}, <!--{$foo}-->`, etc.
* The `{if}..{elseif}..{else}..{/if}` constructs are passed to the PHP parser, so the `{if...}` expression syntax can be as simple or as complex an evaluation as you like.
* Allows unlimited nesting of sections, if's etc.
* Built-in caching support.
* Arbitrary template sources.
* Template Inheritance for easy management of template content.
* Plugin architecture.

See the [Smarty3 Manual](http://www.smarty.net/docs/en/) for other features and information on it's syntax, configuration and installation.

What is SmartyBundle?
---------------------

SmartyBundle is a module that allows the usage of the Smarty template engine in the [Symfony2][@symfony] and [PPI2][@ppi] frameworks.

Authors
-------

### Smarty authors

https://code.google.com/p/smarty-php/people/list

### noiselabs/smarty authors

Vítor Brandão - <vitor@noiselabs.org> ~ [twitter.com/noiselabs](http://twitter.com/noiselabs) ~ [www.noiselabs.org](http://noiselabs.org)

See also the list of [contributors](https://github.com/noiselabs/smarty/contributors) who participated in this project.

Submitting bugs and feature requests
------------------------------------

Smarty issues should be reported to its [own bugtracker on GoogleCode](https://code.google.com/p/smarty-php/issues/list).

Specific issues regarding this repository may be reported here on [GitHub](https://github.com/noiselabs/smarty/issues).
