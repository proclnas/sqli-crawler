Automating a sqli crawler.
---

A simple scan to show how to automatize a simple
task to grab sites with security issues(sqli) using
simple pattern matching and a bing crawler.

![PHP](https://img.shields.io/badge/PHP-5.3%2B-blue.svg)
![OS](https://img.shields.io/badge/Supported%20OS-Linux%20%7C%20Win-orange.svg)

### Dependencies
* php5-curl

### Usage

- Bing scan
```
php main.php -d dork -o uri_results

Params:

-d dork                 | dork to search into bing engine
-o uri_results          | results from bing
```

- Sqli scan
```
php main.php -f uri_file -o output

Params:

-f uri_file            | File with uris to check sql message errors.
-o output              | Result with possible vulns.
```

### License
```
The MIT License (MIT)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```