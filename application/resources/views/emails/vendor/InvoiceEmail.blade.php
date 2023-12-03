@php
    $variableArray = array(
    'transaction_id'=> $transaction_id,
    'vendor_logo'=>$vendor_logo,
    'vendor_name'=>$vendor_name,
    'vendor_street_name'=>$vendor_street_name,
    'vendor_city'=>$vendor_city,
    'vendor_state'=>$vendor_state,
    'vendor_zip_code'=>$vendor_zip_code,
    'transaction_bar_qr_code'=>$transaction_bar_qr_code,
    'transaction_date'=>$transaction_date,
    'transaction_time'=>$transaction_time,
    'transaction_payment_method'=>$transaction_payment_method,
    'transaction_payment_ref'=>$transaction_payment_ref,
    'transaction_auth_id'=>$transaction_auth_id,
    'transaction_sub_total'=>$transaction_sub_total,
    'transaction_tax'=>$transaction_tax,
    'transaction_total'=>$transaction_total,
    'customer_name'=>$customer_name,
    'customer_street_name'=>$customer_street_name,
    'customer_city'=>$customer_city,
    'customer_state'=>$customer_state,
    'customer_zip_code'=>$customer_zip_code,
    'customer_phone'=>$customer_phone,
    'customer_email'=>$customer_email,
    'product_listing'=>$product_listing,
    'link'=>$link
  );

  $templateHTML = $template['content'];
  foreach ($variableArray as $key => $value) {
  $templateHTML = str_replace("{".$key."}", $value, $templateHTML);
  }


@endphp

{!! $templateHTML !!}