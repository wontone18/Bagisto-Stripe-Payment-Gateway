# Bagisto Stripe Payment Gateway
Stripe is a popular payment gateway. This package provides strong support for users to integrate the Stripe payment gateway into their Bagisto Laravel e-commerce applications.

## Installation
1. Use the command prompt to install this package:
```sh
composer require wontonee/stripe
```

2. Open `config/app.php` and register the Payu provider.
```sh
'providers' => [
        // Stripe provider
        Wontonee\Stripe\Providers\StripeServiceProvider::class,
]
```

3. Navigate to the `admin panel -> Configure/Payment Methods`, where Stripe will be visible at the end of the payment method list.

4. Now open `app\Http\Middleware\VerifyCsrfToken.php` and add this route to the exception list.
```sh
protected $except = [
    '/stripe-success',
    '/stripe-cancel'
];
```

5. Now run `php artisan config:cache`



## Troubleshooting

1. If you encounter an issue where you are not redirected to the payment gateway after placing an order and receive a route error, navigate to `bootstrap/cache` and delete all cache files.


For any help or customization, visit <https://www.wontonee.com> or email us <dev@wontonee.com>
