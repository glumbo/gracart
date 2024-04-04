<?php
use Glumbo\Gracart\Front\Models\ShopCountry;

/**
 * Function process after order success
 */
if (!function_exists('gc_order_process_after_success') && !in_array('gc_order_process_after_success', config('helper_except', []))) {
    function gc_order_process_after_success(string $orderID = ""):array
    {
        $templatePath = 'templates.' . gc_store('template');
        if ((gc_config('order_success_to_admin') || gc_config('order_success_to_customer')) && gc_config('email_action_mode')) {
            $data = \Glumbo\Gracart\Front\Models\ShopOrder::with('details')->find($orderID)->toArray();
            $checkContent = (new \Glumbo\Gracart\Front\Models\ShopEmailTemplate)->where('group', 'order_success_to_admin')->where('status', 1)->first();
            $checkContentCustomer = (new \Glumbo\Gracart\Front\Models\ShopEmailTemplate)->where('group', 'order_success_to_customer')->where('status', 1)->first();
            if ($checkContent || $checkContentCustomer) {
                $orderDetail = '';
                $orderDetail .= '<tr>
                                    <td>' . gc_language_render('email.order.sort') . '</td>
                                    <td>' . gc_language_render('email.order.sku') . '</td>
                                    <td>' . gc_language_render('email.order.name') . '</td>
                                    <td>' . gc_language_render('email.order.price') . '</td>
                                    <td>' . gc_language_render('email.order.qty') . '</td>
                                    <td>' . gc_language_render('email.order.total') . '</td>
                                </tr>';
                foreach ($data['details'] as $key => $detail) {
                    $product = (new \Glumbo\Gracart\Front\Models\ShopProduct)->getDetail($detail['product_id']);
                    $pathDownload = $product->downloadPath->path ?? '';
                    $nameProduct = $detail['name'];
                    if ($product && $pathDownload && $product->property == GC_PROPERTY_DOWNLOAD) {
                        $nameProduct .="<br><a href='".gc_path_download_render($pathDownload)."'>Download</a>";
                    }

                    $orderDetail .= '<tr>
                                    <td>' . ($key + 1) . '</td>
                                    <td>' . $detail['sku'] . '</td>
                                    <td>' . $nameProduct . '</td>
                                    <td>' . gc_currency_render($detail['price'], '', '', '', false) . '</td>
                                    <td>' . number_format($detail['qty']) . '</td>
                                    <td align="right">' . gc_currency_render($detail['total_price'], '', '', '', false) . '</td>
                                </tr>';
                }
                $dataFind = [
                    '/\{\{\$title\}\}/',
                    '/\{\{\$orderID\}\}/',
                    '/\{\{\$firstName\}\}/',
                    '/\{\{\$lastName\}\}/',
                    '/\{\{\$toname\}\}/',
                    '/\{\{\$address\}\}/',
                    '/\{\{\$address1\}\}/',
                    '/\{\{\$address2\}\}/',
                    '/\{\{\$address3\}\}/',
                    '/\{\{\$email\}\}/',
                    '/\{\{\$phone\}\}/',
                    '/\{\{\$comment\}\}/',
                    '/\{\{\$orderDetail\}\}/',
                    '/\{\{\$subtotal\}\}/',
                    '/\{\{\$shipping\}\}/',
                    '/\{\{\$discount\}\}/',
                    '/\{\{\$otherFee\}\}/',
                    '/\{\{\$total\}\}/',
                ];
                $dataReplace = [
                    gc_language_render('email.order.email_subject_customer') . '#' . $orderID,
                    $orderID,
                    $data['first_name'],
                    $data['last_name'],
                    $data['first_name'].' '.$data['last_name'],
                    $data['address1'] . ' ' . $data['address2'].' '.$data['address3'],
                    $data['address1'],
                    $data['address2'],
                    $data['address3'],
                    $data['email'],
                    $data['phone'],
                    $data['comment'],
                    $orderDetail,
                    gc_currency_render($data['subtotal'], '', '', '', false),
                    gc_currency_render($data['shipping'], '', '', '', false),
                    gc_currency_render($data['discount'], '', '', '', false),
                    gc_currency_render($data['other_fee'], '', '', '', false),
                    gc_currency_render($data['total'], '', '', '', false),
                ];

                // Send mail order success to admin
                if (gc_config('order_success_to_admin') && $checkContent) {
                    $content = $checkContent->text;
                    $content = preg_replace($dataFind, $dataReplace, $content);
                    $dataView = [
                        'content' => $content,
                    ];
                    $config = [
                        'to' => gc_store('email'),
                        'subject' => gc_language_render('email.order.email_subject_to_admin', ['order_id' => $orderID]),
                    ];
                    gc_send_mail($templatePath . '.mail.order_success_to_admin', $dataView, $config, []);
                }

                // Send mail order success to customer
                if (gc_config('order_success_to_customer') && $checkContentCustomer && $data['email']) {
                    $contentCustomer = $checkContentCustomer->text;
                    $contentCustomer = preg_replace($dataFind, $dataReplace, $contentCustomer);
                    $dataView = [
                        'content' => $contentCustomer,
                    ];
                    $config = [
                        'to' => $data['email'],
                        'replyTo' => gc_store('email'),
                        'subject' => gc_language_render('email.order.email_subject_customer', ['order_id' => $orderID]),
                    ];

                    $attach = [];
                    if (gc_config('order_success_to_customer_pdf')) {
                        // Invoice pdf
                        \PDF::loadView($templatePath . '.mail.order_success_to_customer_pdf', $dataView)
                            ->save(\Storage::disk('invoice')->path('order-'.$orderID.'.pdf'));
                        $attach['attachFromStorage'] = [
                            [
                                'file_storage' => 'invoice',
                                'file_path' => 'order-'.$orderID.'.pdf',
                            ]
                        ];
                    }

                    gc_send_mail($templatePath . '.mail.order_success_to_customer', $dataView, $config, $attach);
                }
            }
        }
        $dataResponse = [
            'orderID'        => $orderID,
        ];
        return $dataResponse;
    }
}

/**
 * Function process mapping validate order
 */
if (!function_exists('gc_order_mapping_validate') && !in_array('gc_order_mapping_validate', config('helper_except', []))) {
    function gc_order_mapping_validate():array
    {
        $validate = [
            'first_name'     => config('validation.customer.first_name', 'required|string|max:100'),
            'email'          => config('validation.customer.email', 'required|string|email|max:255'),
        ];
        //check shipping
        if (!gc_config('shipping_off')) {
            $validate['shippingMethod'] = 'required';
        }
        //check payment
        if (!gc_config('payment_off')) {
            $validate['paymentMethod'] = 'required';
        }

        if (gc_config('customer_lastname')) {
            if (gc_config('customer_lastname_required')) {
                $validate['last_name'] = config('validation.customer.last_name_required', 'required|string|max:100');
            } else {
                $validate['last_name'] = config('validation.customer.last_name_null', 'nullable|string|max:100');
            }
        }
        if (gc_config('customer_address1')) {
            if (gc_config('customer_address1_required')) {
                $validate['address1'] = config('validation.customer.address1_required', 'required|string|max:100');
            } else {
                $validate['address1'] = config('validation.customer.address1_null', 'nullable|string|max:100');
            }
        }

        if (gc_config('customer_address2')) {
            if (gc_config('customer_address2_required')) {
                $validate['address2'] = config('validation.customer.address2_required', 'required|string|max:100');
            } else {
                $validate['address2'] = config('validation.customer.address2_null', 'nullable|string|max:100');
            }
        }

        if (gc_config('customer_address3')) {
            if (gc_config('customer_address3_required')) {
                $validate['address3'] = config('validation.customer.address3_required', 'required|string|max:100');
            } else {
                $validate['address3'] = config('validation.customer.address3_null', 'nullable|string|max:100');
            }
        }

        if (gc_config('customer_phone')) {
            if (gc_config('customer_phone_required')) {
                $validate['phone'] = config('validation.customer.phone_required', 'required|regex:/^0[^0][0-9\-]{6,12}$/');
            } else {
                $validate['phone'] = config('validation.customer.phone_null', 'nullable|regex:/^0[^0][0-9\-]{6,12}$/');
            }
        }
        if (gc_config('customer_country')) {
            $arrayCountry = (new ShopCountry)->pluck('code')->toArray();
            if (gc_config('customer_country_required')) {
                $validate['country'] = config('validation.customer.country_required', 'required|string|min:2').'|in:'. implode(',', $arrayCountry);
            } else {
                $validate['country'] = config('validation.customer.country_null', 'nullable|string|min:2').'|in:'. implode(',', $arrayCountry);
            }
        }

        if (gc_config('customer_postcode')) {
            if (gc_config('customer_postcode_required')) {
                $validate['postcode'] = config('validation.customer.postcode_required', 'required|min:5');
            } else {
                $validate['postcode'] = config('validation.customer.postcode_null', 'nullable|min:5');
            }
        }
        if (gc_config('customer_company')) {
            if (gc_config('customer_company_required')) {
                $validate['company'] = config('validation.customer.company_required', 'required|string|max:100');
            } else {
                $validate['company'] = config('validation.customer.company_null', 'nullable|string|max:100');
            }
        }

        if (gc_config('customer_name_kana')) {
            if (gc_config('customer_name_kana_required')) {
                $validate['first_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
            } else {
                $validate['first_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
            }
        }

        $messages = [
            'last_name.required'      => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.last_name')]),
            'first_name.required'     => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.first_name')]),
            'email.required'          => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.email')]),
            'address1.required'       => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.address1')]),
            'address2.required'       => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.address2')]),
            'address3.required'       => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.address3')]),
            'phone.required'          => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.phone')]),
            'country.required'        => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.country')]),
            'postcode.required'       => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.postcode')]),
            'company.required'        => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.company')]),
            'sex.required'            => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.sex')]),
            'birthday.required'       => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.birthday')]),
            'email.email'             => gc_language_render('validation.email', ['attribute'=> gc_language_render('cart.email')]),
            'phone.regex'             => gc_language_render('customer.phone_regex'),
            'postcode.min'            => gc_language_render('validation.min', ['attribute'=> gc_language_render('cart.postcode')]),
            'country.min'             => gc_language_render('validation.min', ['attribute'=> gc_language_render('cart.country')]),
            'first_name.max'          => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.first_name')]),
            'email.max'               => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.email')]),
            'address1.max'            => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.address1')]),
            'address2.max'            => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.address2')]),
            'address3.max'            => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.address3')]),
            'last_name.max'           => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.last_name')]),
            'birthday.date'           => gc_language_render('validation.date', ['attribute'=> gc_language_render('cart.birthday')]),
            'birthday.date_format'    => gc_language_render('validation.date_format', ['attribute'=> gc_language_render('cart.birthday')]),
            'shippingMethod.required' => gc_language_render('cart.validation.shippingMethod_required'),
            'paymentMethod.required'  => gc_language_render('cart.validation.paymentMethod_required'),
        ];

        $dataMap['validate'] = $validate;
        $dataMap['messages'] = $messages;

        return $dataMap;
    }
}