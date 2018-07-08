# Fashion Journal

<img align="right" src="https://raw.github.com/3bola/gulp-starter/master/app/img/pipboy.jpg" hspace="20" vspace="10" width="320">

A front-end Gulp compiler for Wordpress application and Automatic deploy to AWS Code Pipeline

* Inspired by: [http://github.com/3bola/gulp-starter](http://github.com/3bola/gulp-starter)
* Visit production server: [http://fj.nativesdev.com.au](http://fj.nativesdev.com.au)

### Features

* Original Wordpress application structure
* Livereloading development server with automatic building of SCSS
* Bower component management
* Automatic image compressing
* SCSS compiling
* CSS autoprefixing, combining and minifying
* JavaScript combining and compressing with uglify
* Automatic deploy to AWS Code Pipeline with AWS CodeCommit, CodeBuild and CodeDeploy

### Usage

#### Prerequisite

* Make sure you have latest [NodeJS](http://nodejs.org/) installed.
* Install [Chrome LiveReload](https://chrome.google.com/webstore/detail/livereload/jnihajbhpnppcggbcgedagnkighmdlei?hl=en) plugin. (Optional)
* Download or use your current [Word Press](https://wordpress.org/download/) template.
* Download [XAMPP](https://www.apachefriends.org/download.html) and setup PHP environment [Apache and MySQL](https://netbeans.org/kb/docs/php/configure-php-environment-windows.html)
* Add the folder `C:\xampp\mysql\bin` to the $PATH variable
* Run Apache and MySQL, then open [phpmyadmin](http://localhost/phpmyadmin) and create a blank DB named "wordpress"

#### Installation

NOTE: before following the steps below please:

1. Complete the Prerequisite steps

2. Locate your `htdocs` folder. Then open Command Prompt and type:

```sh
git clone git@github.com:nichealpham/fashion-journal.git /***/***/htdocs
cd /***/***/htdocs
npm install && npm run init
```

Then using XAMPP Control Pannel and start Apache and mySQL and go to your browser to open [http://localhost](http://localhost)!

#### Structure

```sh

htdocs:
- |__ .vscode
- |__ .gitignore
- |
- |__ wp-admin-------------------------// Wordpress folder.
- |     |__ [...]
- |
- |__ wp-content-----------------------// Wordpress templates holder
- |     |__ themes
- |     |__ plugins
- |     |__ index.php
- |
- |__ wp-includes----------------------// Wordpress plugins
- |     |__ [...]
- |
- |__ wp-db----------------------------// Store exported .sql and .bat file for server import
- |     |__ fashionJournalDb.sql-------// exported .sql file
- |     |__ installDb.bat--------------// .bat file import .sql file into mySQL on server
- |
- |__ wp-[...]-------------------------// Wordpress files.
- |
- |__ webpackage-----------------------// Source of templates development.
- |     |__ wp-content---------------- // Preserve the structure of root wp-content folder
- |         |                          // Used for template development
- |         |__ themes
- |         |__ plugins
- |         |__ index.php
- |
- |__ .node_modules--------------------// Nodejs modules for gulp
- |
- |__ gulpfile.js----------------------// Front-end compilation file
- |__ bower.json-----------------------// Bower package manager for gulp
- |__ package.json---------------------// npm scripts for development and deployment


```

#### Commands

NOTE: for any of these command to run, remember to start Apache and MySQL !

To build your code and see changes in localhost, run:

```sh
npm start
```

To export DB before commit to staging, run:

```sh
npm run export
```

To import DB after pulling from origin, run:

```sh
npm run import
```

To create a .zip release, run:

```sh
npm run release
```

#### Development

* In wordpress, template files are stored in folder `wp-content`
* Your template files should ONLY be located in the folder `htdocs\webpackage\wp-content`
* CAUTION: Any `*.js`, `*.css` and `*.scss` modified outside the folder `htdocs\webpackage\wp-content` will not be compiled and compressed !

NOTE: There are two types of action when using this source:

* Action that does not change DB: such as changing JS, CSS, SCSS files:

To build your code and see changes in localhost, run:

```sh
npm start
```

* Action that changes DB: such as importing pluggins, add themes:

Remember to export all changes from your current DB into .sql file before committing to staging server

```sh
npm run export
```

#### Deployment

In other to deploy, merge code from master branch to the corresponding branches.

##### Steps to server deployment

Make sure you are on the master branch:

```sh
git check out master
```

Make sure you have the lastest code:

```sh
git pull origin master
```

Then swith to your desired branch:

```sh
git check out staging
```

Merge code from master to staging

```sh
git merge master
```

After merging, developers can swith back to master and continue working:

```sh
git check out master
```

AWS CodePipeline will automatically compile neccessary files and deloy them to the corresponding servers.

#### Codeflow

![Image structure not found](https://github.com/nichealpham/Open-Source-Projects/blob/master/Wordpress-aws-code-pipeline/webpackage/app-structure.png)<br/>

When `npm start` is runned, it does the following:

1. Migrate all files and structure from `htdocs\webpackage\wp-content` to `htdocs\wp-content`

2. Inside the `htdocs\wp-content` folder, find all `main.scss` files and compile them into `main.css` files

3. Inside the `htdocs\wp-content` folder, find all `*.scss` and `*.js` files and compress them

4. Inside the `htdocs\wp-content` folder, find all `*.img, *.png, *.jpeg` and compress them

When `git commit` is runned, it does the following:

1. Update changes to the `AWS S3 Bucket` via `AWS CodeCommit`

2. Then `AWS CodeBuild` will be runned to:

    + Update MySQL Schema to Server
    + Locate all `function.php` files and change the string from `http:\\localhost` to `http://fj-sg.nativesdev.com.au`

3. Then `AWS CodeDeploy` will deploy the repository to `AWS EC2 Instance` for Staging

4. If tested thoroughly, `AWS CodeDeploy` will continue to deploy it to the Production Server

#### Configuration

You can find some useful options in the `gulpfile.js` file.

### License

#### The MIT License (MIT)

Copyright (c) 2017 Nicheal Pham

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
