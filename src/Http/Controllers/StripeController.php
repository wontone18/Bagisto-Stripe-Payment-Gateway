<?php


namespace Wontonee\Stripe\Http\Controllers;


use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Stripe\Stripe;

class StripeController extends Controller
{

  /**
   * OrderRepository $orderRepository
   *
   * @var \Webkul\Sales\Repositories\OrderRepository
   */
  protected $orderRepository;
  /**
   * InvoiceRepository $invoiceRepository
   *
   * @var \Webkul\Sales\Repositories\InvoiceRepository
   */
  protected $invoiceRepository;

  /**
   * Create a new controller instance.
   *
   * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
   * @return void
   */
  public function __construct(OrderRepository $orderRepository,  InvoiceRepository $invoiceRepository)
  {
    $this->orderRepository = $orderRepository;
    $this->invoiceRepository = $invoiceRepository;
  }

  /**
   * Redirects to the paytm server.
   *
   * @return \Illuminate\View\View
   */

  public function redirect()
  {


    $cart = Cart::getCart();
    $billingAddress = $cart->billing_address;
    Stripe::setApiKey(core()->getConfigData('sales.payment_methods.stripe.stripe_api_key'));

    $shipping_rate = $cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0; // shipping rate
    $discount_amount = $cart->discount_amount; // discount amount
    $total_amount =  ($cart->sub_total + $cart->tax_total + $shipping_rate) - $discount_amount; // total amount

    $checkout_session = \Stripe\Checkout\Session::create([
      'line_items' => [[
        'price_data' => [
          'currency' => $cart->global_currency_code,
          'product_data' => [
            'name' => 'Stripe Checkout Payment order id - ' . $cart->id,
          ],
          'unit_amount' => $total_amount * 100,
        ],
        'quantity' => 1,
      ]],
      'payment_method_types' => [
        'card',
      ],
      'mode' => 'payment',
      'success_url' => route('stripe.success'),
      'cancel_url' => route('stripe.cancel'),
    ]);

    return redirect()->away($checkout_session->url);
  }

  /**
   * success
   */
  public function success()
  {
    $order = $this->orderRepository->create(Cart::prepareDataForOrder());
    $this->orderRepository->update(['status' => 'processing'], $order->id);
    if ($order->canInvoice()) {
      $this->invoiceRepository->create($this->prepareInvoiceData($order));
    }
    Cart::deActivateCart();
    session()->flash('order', $order);
    // Order and prepare invoice
    return redirect()->route('shop.checkout.onepage.success');
  }


  /**
   * failure
   */
  public function failure()
  {

    echo ('jsjjs');
    exit;
    session()->flash('error', 'Strpe payment either cancelled or transaction failure.');
    return redirect()->route('shop.checkout.cart.index');
  }

  /**
   * Prepares order's invoice data for creation.
   *
   * @return array
   */
  protected function prepareInvoiceData($order)
  {
    $invoiceData = ["order_id" => $order->id,];

    foreach ($order->items as $item) {
      $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
    }

    return $invoiceData;
  }
}
