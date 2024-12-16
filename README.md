# guzzle-sample

## Installation

1. Clone the repository
```sh
git clone git@github.com:shimabox/guzzle-sample.git
```
2. Run `make setup`
```sh
cd guzzle-sample
make setup
```

## Usage

1. Run `make client` to enter the client-side container.
```sh
make client
```
2. Exec `index.php` or web access http://localhost:8080
```sh
php index.php
```

## Sample

- sample_1
  - ```sh
    make client
    php src/sample_1.php
    ```
- sample_2
  - ```sh
    make client
    php src/sample_2.php
    ```
- sample_3
  - ```sh
    make client
    php src/sample_3.php
    ```
- sample_3_example_then.php
  - ```sh
    make client
    php src/sample_3_example_then.php
    ```
- sample_4
  - ```sh
    make client
    php src/sample_4.php
    ```
- sample_4_example_key.php
  - ```sh
    make client
    php src/sample_4_example_key.php
    ```
- sample_guzzle
  - ```sh
    make client
    php src/Sample/index.php
    ```
- sample_guzzle(throttle error)
  - ```sh
    make client
    php src/Sample/throttle_error.php
    ```
## Test

- test
  - ```shell
    client-test
    ```
- coverage
  - ```shell
    make client-test-coverage
    ```
  - check `client/coverage/index.html`.
