# WHMCS Currency Exchange Rate Auto Update

This PHP script automatically updates the exchange rate of a specified currency in your WHMCS installation using the Flutterwave Transfer Rate API. It is designed to be run as a GET request via a URL, making it easy to set up as a cron job.

## Features

- Automatically updates the exchange rate between a source currency and your WHMCS base currency.
- Utilizes the Flutterwave API to get the latest transfer rates.
- Designed to be set up as a GET request cron job.

## Requirements

- WHMCS installation
- PHP 7.4 or higher
- Curl enabled in PHP
- MySQL database
- Flutterwave secret key

## Installation

1. **Clone or Download the Repository:**

   ```bash
   git clone https://github.com/JosephChuks/whmcs-flutterwave-currency-update.git
   ```
2. Place the script in your WHMCS root directory so it can be accessed via a URL like `http://yourdomain.com/rates`.

## Configuration:

**Flutterwave Secret Key:** Set your Flutterwave secret key in the script. You can directly assign it in the $flutterwaveSecret variable or retrieve it from your WHMCS configuration file.

**Currency Configuration:** Modify the $sourceCurrency and $destinationCurrency variables to match your desired currencies.

**Database Connection:** The script automatically imports your WHMCS database configuration from configuration.php.

## Cron Job Setup:

To automate the currency rate update, set up a cron job to access the script URL at your desired intervals.


Example cron job to run the script every hour: `0 * * * * wget -qO- http://yourdomain.com/rates >/dev/null 2>&1`.

Once configured, the script will fetch the current exchange rate from Flutterwave's API and update the corresponding currency rate in your WHMCS installation.

The script returns a JSON response indicating success or failure:

Success Response:
```{
    "status": "success",
    "message": "Current USD Rate to NGN: 1 USD = X.XX NGN"
}
```

Error Response:
```
{
    "status": "error",
    "message": "Error message"
}
```

## Contributing
If you'd like to contribute to this project, please fork the repository and use a feature branch. Pull requests are warmly welcome.

## License
This project is licensed under the MIT License. See the LICENSE file for details.


