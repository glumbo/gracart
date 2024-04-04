<?php
use Glumbo\Gracart\Events\OrderSuccess;
use Glumbo\Gracart\Events\OrderCreated;
use Glumbo\Gracart\Events\CustomerCreated;
use Glumbo\Gracart\Events\OrderUpdateStatus;
use Glumbo\Gracart\Front\Models\ShopOrder;
use Glumbo\Gracart\Front\Models\ShopCustomer;

if (!function_exists('gc_event_order_success') && !in_array('gc_event_order_success', config('helper_except', []))) {
    /**
     * Process order event
     *
     * @return  [type]          [return description]
     */
    function gc_event_order_success(ShopOrder $order)
    {
        OrderSuccess::dispatch($order);
    }
}

if (!function_exists('gc_event_order_created') && !in_array('gc_event_order_created', config('helper_except', []))) {
    /**
     * Process order event
     *
     * @return  [type]          [return description]
     */
    function gc_event_order_created(ShopOrder $order)
    {
        OrderCreated::dispatch($order);
    }
}

if (!function_exists('gc_event_order_update_status') && !in_array('gc_event_order_update_status', config('helper_except', []))) {
    /**
     * Process event order update status
     *
     * @return  [type]          [return description]
     */
    function gc_event_order_update_status(ShopOrder $order)
    {
        OrderUpdateStatus::dispatch($order);
    }
}

if (!function_exists('gc_event_customer_created') && !in_array('gc_event_customer_created', config('helper_except', []))) {
    /**
     * Process customer event
     *
     * @return  [type]          [return description]
     */
    function gc_event_customer_created(ShopCustomer $customer)
    {
        CustomerCreated::dispatch($customer);
    }
}

