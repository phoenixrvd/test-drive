# Test drive

An automation tool that allows to formalize the manual testing of web applications.

## Installation

Selenium WebDriver is used in the background of the tool, so it must be installed.

* [chromedriver](https://chromedriver.chromium.org/home) - for GoogleChrome and Mobile-Emulation
* [geckodriver](https://github.com/mozilla/geckodriver) - for Firefox testing

Once the WebDrivers are installed, the project can be installed using composer.

```shell
composer require phoenixrvd/test-drive
```

## First run

```shell
./bin/test-drive -h
```

## Configuration

All configuration variables can be found under [examples/.env.defaults]. To store a different configuration you have to create an
.env file in the project root and overwrite the corresponding settings in this file.

## Copyright and license

Code released under the [MIT License](LICENSE). 
