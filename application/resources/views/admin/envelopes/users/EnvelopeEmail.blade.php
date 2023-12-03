@php
    $variableArray = array(
    'envelope_id' => $envelope_id,
    );
    
    $templateHTML = $template['content'];
    foreach ($variableArray as $key => $value){
    $templateHTML = str_replace("{".$key."}", $value, $templateHTML);
    }
@endphp

{!! $templateHTML !!}