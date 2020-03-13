# Vinti4 Checkout for Cockpit CMS

## Installation
Install Cockpit CMS addon by extracting to the addons folder (/addons/Vinti4)

### Install Dependencies

```
$ cd /addons/Vinti4
$ composer install
```

### Add Checkout Config

```
vinti4:
    posID: YOUR_POS_ID
    posAutCode: YOUR_AUTH_CODE
    merchantID: YOUR_MERCHANT_ID,
    urlMerchantResponse: YOUR_CALLBACK_URL
```