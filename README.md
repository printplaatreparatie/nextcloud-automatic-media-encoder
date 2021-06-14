# Automatic Media Encoder

![Kapture 2021-06-09 at 22 45 58](https://user-images.githubusercontent.com/13686317/121471489-a1a0ee80-c974-11eb-9150-4ab03376e8a5.gif)

## Getting Started

[ffmpeg]() is a required dependency to support media conversion.  

If you are using Docker, create a custom `Dockerfile` that extends the base Nextcloud image to install `ffmpeg`.

This should be installed _before_ you install this app in Nextcloud.

## After Installation

Once the app has been installed in Nextcloud, you may go to Settings &gt; Automated Media Converter to add photo or video conversion rules.

## How It Works

There are two background jobs working as a pair.

The first job is `FindNewMedia` which runs once every minute to do a quick recursive seek to find unconverted media in specified folders and schedule them for conversion.

<img width="1067" alt="image" src="https://user-images.githubusercontent.com/13686317/121822502-414fcc80-cc54-11eb-8e23-a29a80725d11.png">

The second job is `ConvertMedia` which spawns a single `ffmpeg` process to convert a file found in `FindNewMedia`.

<img width="606" alt="image" src="https://user-images.githubusercontent.com/13686317/121822323-6859ce80-cc53-11eb-8186-fddbf82ec64e.png">

## Development

### Building the app

The app can be built by using the provided Makefile by running:

    make

This requires the following things to be present:
* make
* which
* tar: for building the archive
* curl: used if phpunit and composer are not installed to fetch them from the web
* npm: for building and testing everything JS, only required if a package.json is placed inside the **js/** folder

The make command will install or update Composer dependencies if a composer.json is present and also **npm run build** if a package.json is present in the **js/** folder. The npm **build** script should use local paths for build systems and package managers, so people that simply want to build the app won't need to install npm libraries globally, e.g.:

**package.json**:
```json
"scripts": {
    "test": "node node_modules/gulp-cli/bin/gulp.js karma",
    "prebuild": "npm install && node_modules/bower/bin/bower install && node_modules/bower/bin/bower update",
    "build": "node node_modules/gulp-cli/bin/gulp.js"
}
```


### Publish to App Store

First get an account for the [App Store](http://apps.nextcloud.com/) then run:

    make && make appstore

The archive is located in build/artifacts/appstore and can then be uploaded to the App Store.

### Running tests
You can use the provided Makefile to run all tests by using:

    make test

This will run the PHP unit and integration tests and if a package.json is present in the **js/** folder will execute **npm run test**

Of course you can also install [PHPUnit](http://phpunit.de/getting-started.html) and use the configurations directly:

    phpunit -c phpunit.xml

or:

    phpunit -c phpunit.integration.xml

for integration tests
