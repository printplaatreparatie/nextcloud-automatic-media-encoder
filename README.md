# Automatic Media Encoder

## Getting Started

[ffmpeg]() is a required dependency to support media conversion.  

If you are using Docker, create a custom `Dockerfile` that extends the base Nextcloud image to install `ffmpeg`.

This should be installed _before_ you install this app in Nextcloud.

## After Installation

Once the app has been installed in Nextcloud, you may go to Settings &gt; Automated Media Converter to add photo or video conversion rules.

## How It Works

There are two background jobs working as a pair.

The first job is `FindNewMedia` which runs once every minute to do a quick recursive seek to find unconverted media in specified folders and schedule them for conversion.

The second job is `ConvertMedia` which runs on a configurable schedule to iterate over unconverted media scheduled by `FindNewMedia` in chronological order and spawn an `ffmpeg` process to convert the media based on the rules specified in Settings &gt; Automated Media Converter followed by the logic specified in the rule.

The `ConvertMedia` job tries to convert as many media as it can before the next scheduled job, which will pick up where the last job left off, and can be scheduled to run as often as you like, opening up the following possibilities:

### 1. Convert media as often as possible (say once a minute)
If you are uploading short videos or have very fast hardware, you can get near real time video processing with this configuration.   However, it will likely fail to finish converting larger videos within the minute and retry infinitely.

### 2. Convert media semi-often (say once every 60 minutes)
This is a good balance between real-time processing and resource management.  It is likely most videos can be converted within 60 minutes on modern hardware.

### 3. Convert media nightly
This isn&#39;t likely useful in most scenarios, but could help when converting many long videos.

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
