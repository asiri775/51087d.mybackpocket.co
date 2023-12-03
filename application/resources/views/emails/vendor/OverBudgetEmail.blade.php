@php

$variableArray = array(
    'budgetLimit'=> $budgetLimit,
    'sum'=>$sum,
);

$templateHTML = $template['content'];
  foreach ($variableArray as $key => $value) {
  $templateHTML = str_replace("{".$key."}", $value, $templateHTML);
  }


@endphp

{!! $templateHTML !!}