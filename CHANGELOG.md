# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## Unreleased

* Add default handlers for POST /ses/v2/email/outbound-emails and POST /sendgrid/v3/mail/send
* Add built-in support for reading / writing data during test runs 
* Support both `/ping-200` and `/ping-200/{any child path}` for simple cases that just need a 200
