<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>DeliverDas Invoice</title>
    
    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
       
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;

    }
    
    .invoice-box table tr td:nth-child(2) {
        text-align: right;

    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 20px;

    }
    
    .invoice-box table tr.top table td.title {
        font-size: 20px;
        line-height: 10px;
        color: #333;
        
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    
    .invoice-box table tr.heading td {
        background: #cdd8f9;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }

    </style>
</head>

<body>


    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <h1>DeliverDas</h1>
                            </td>
                            
                            <td>
                                Invoice #: {{ $order_number }}<br>
                                Created: {{ date('d F, Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                {{ isset($user_address['billingAddress']['address_1']) ? $user_address['billingAddress']['address_1'] : null }},{{ isset($user_address['billingAddress']['address_2']) ? $user_address['billingAddress']['address_2'] : null }}, {{ $user_address['billingAddress']['city']  }} , <br/> {{ $user_address['billingAddress']['country_name'] }}<br>
                                {{ $user_address['billingAddress']['postcode'] }}
                            </td>
                            
                            <td>
                                {{ $store_name }}<br>
                                {{ $store_user_email}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    Payment Method
                </td>
                <td>
                     {{ $user_address['gateway'] }}
                </td>
            </tr>
            
            <tr class="details">
                <td>
                    {{ $user_address['gateway'] }}
                </td>
                
                <td>
                    {{ isset($user_address['payment_status']) ? $user_address['payment_status'] : $user_address['paymentStatus'] }}
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    Item
                </td>
                
                <td>
                    Price
                </td>
                <td>
                    Quantity
                </td>
                <td>
                    Total Price
                </td>
            </tr>
            
            <?php  foreach($order_items as $order) { ?>
                <tr class="item">
                 <td>
                    {{ $order['productName'] }}
                </td>
                
                <td>
                    &#8377; {{ $order['price'] }}
                </td>
                <td>
                   {{ $order['quantity'] }}
                </td>
                <td>
                    &#8377;{{ ($order['price'] *  $order['quantity']) }}
                </td>
		 </tr>
            <?php } ?>
		<tr class="total">
                    <td></td>
                
                    <td>
                       Total: &#8377; {{ $totalamt }}
                    </td>
                    
                    <td>
                       Delivery Charge: &#8377; 10
                    </td>
                    
                    <td>
                      Total : &#8377; {{ ($totalamt + 10) }}
                    </td>
            </tr>
        </table>
    </div>
</body>
</html>
