# Cellular-Machine

Cellular Machine is an application which provide all necessary computations and transformations of polynomial pattern required for Shannon's entropy chart.
All of that is wrapped into simple and clear web view. Application supports custom polynomial patterns as well as patterns described by Ulam function.




## Requirements

PHP 8.0 or higher is required to avoid potential compatibility errors.


## Installation

#### Linux


Following process assumes Git and required PHP version are installed and configured on your local machine.

Download and unpack ZIP file in destination directory or open terminal and paste:
```
git clone https://github.com/Tiadem/Cellular-Machine.git
```
Head to the project root directory and launch PHP Server:
```
php -S 127.0.0.1:8000 -t ./
```
Application should be available under [localhost](http://localhost:8000/).

#### Windows

Fastest and easiest way to put up a project is by downloading and installing [XAMPP](https://www.apachefriends.org/download.html) stack.

1. Download and unpack ZIP file in your XAMPP htdocs directory.
2. Turn on XAMPP control panel and start Apache server.
3. Application should be available under [localhost](http://localhost/Cellular-Machine).

## Usage

Application provide simple html form to describe patterns which are about to be transformed.
Form accepts following inputs:
1. Custom Pattern which is serialized in following manner:
    - pattern only supports 'x' as a unknown value
    - pattern is divided into polynomial positions with following syntax :

      **sign**:**multiply**:**x**:**power**:**power-value**:**divided-by**
   
      | **Name**    | **Value**             | **Description**                                  | **Required** |
      |-------------|---------------------------|--------------------------------------------------|--------------|
      | sign        | -/+                   | sign of position, default to +                   | Optional     |
      | multiply    | natural number        | multiplication of unknown value                  | Required     |
      | x           | calculated by application | unknown value                                    | Required     |
      | power       | ^                     | power sign                                       | Required     |
      | power-value | natural number        | power sign value                                 | Required     |
      | divided-by  | /natural number       | division of current position, must include slash | Optional     |
      
    - pattern **MIGHT HAVE** additional value at the end of positions chain
    - pattern **MUST HAVE** modulo sign % at the end of positions chain
    - pattern **MUST HAVE** modulo value after modulo sign
2. Time span
3. Modulo for Ulam function

## Contributors
Julia Reterska, Krzysztof Szkopiak


