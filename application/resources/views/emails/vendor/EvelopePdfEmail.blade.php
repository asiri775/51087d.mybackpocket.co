@php

$variableArray = array(
    'customer_name'=> '',
    'vendor_name'=>$envelope_name,
);

$templateHTML = $template['content'];
  foreach ($variableArray as $key => $value) {
  $templateHTML = str_replace("{".$key."}", $value, $templateHTML);
  }


@endphp

{!! $templateHTML !!}