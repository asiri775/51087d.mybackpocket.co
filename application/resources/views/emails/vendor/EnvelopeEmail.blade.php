@php
    $variableArray = array(
    'link'=>$link
  );

  $templateHTML = $template['content'];
  foreach ($variableArray as $key => $value) {
  $templateHTML = str_replace("{".$key."}", $value, $templateHTML);
  }


@endphp

{!! $templateHTML !!}