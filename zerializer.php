<?php

  function zerialize ( $element, $recursive=false ) {
    if ( empty($element) ) { return ( false ); }

    $is_sequential_array = false;
    if ( array_keys( $element ) === range( 0, count($element)-1 ) ) { //If is a sequential array
      $is_sequential_array = true;
    }


    $serialized = "";

    if ( $is_sequential_array ) { $serialized .= '['; }
    else { $serialized .= "{\n"; }

    foreach ( $element as $key => $value ) {

      if ( !$is_sequential_array ) { 
        if ( $recursive == true ) { $serialized .= "  "; } 
        $serialized .= "  " . $key . ":";
      }


      if ( is_array($value) ) {

        $serialized .= zerialize( $value, true ); //Array inside the array, I am going to use recursivity

      } else {
        if ( is_string ($value) ) { $serialized .= '\"'; }
        $serialized .= $value;
        if ( is_string ($value) ) { $serialized .= '\"'; }
      }


      if ( $is_sequential_array ) { $serialized .= ", "; }
      else { $serialized .= ":/" . $key . "\n"; }
    }


    if ( $is_sequential_array ) { $serialized .= ']'; $serialized = str_replace ( ', ]', ']', $serialized ); }
    else { 
      if ( $recursive == true ) { $serialized .= "  "; }
      $serialized .= "}"; 
    }

    return ( $serialized );
  }
