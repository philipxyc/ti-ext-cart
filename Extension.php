<?php namespace SamPoyigi\Cart;

use Event;
use Igniter\Flame\Cart\Cart;
use Illuminate\Foundation\AliasLoader;
use System\Classes\BaseExtension;

class Extension extends BaseExtension
{
    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('Cart', 'Igniter\Flame\Cart\Facades\Cart');

        $config = __DIR__.'/config/cart.php';
        $this->mergeConfigFrom($config, 'cart');

        $this->app->singleton('cart', function ($app) {
            return new Cart($app['session.store'], $app['events']);
        });
    }

    public function initialize()
    {
        Event::listen('admin.order.paymentProcessed', function ($model) {
            $model->mailSend('sampoyigi.cart::mail.order', 'customer');
            $model->mailSend('sampoyigi.cart::mail.order_alert', 'location');
            $model->mailSend('sampoyigi.cart::mail.order_alert', 'admin');
            // Subtract menu quantity and redeem coupon
        });
    }

    public function registerComponents()
    {
        return [
            'SamPoyigi\Cart\Components\CartBox'  => [
                'code'        => 'cartBox',
                'name'        => 'lang:sampoyigi.cart::default.text_component_title',
                'description' => 'lang:sampoyigi.cart::default.text_component_desc',
            ],
            'SamPoyigi\Cart\Components\Checkout' => [
                'code'        => 'checkout',
                'name'        => 'lang:sampoyigi.cart::default.text_checkout_component_title',
                'description' => 'lang:sampoyigi.cart::default.text_checkout_component_desc',
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'Module.CartModule' => [
                'description' => 'Ability to manage cart module',
                'action'      => ['manage'],
            ],
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'sampoyigi.cart::mail.order'       => 'Order confirmation email to customer',
            'sampoyigi.cart::mail.order_alert' => 'New order alert email to admin',
        ];
    }
}