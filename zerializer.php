<?php

                                                                            //ENCODE------
  function zerializeFunction ( $element, $recursive = false ) {
    if ( empty($element) ) { return ( false ); }

    static $functionIterationCounter = 1;

    if ( is_array ($element) ) {
      $is_sequential_array = false;
      if ( array_keys( $element ) === range( 0, count($element)-1 ) ) { //If is a sequential array
        $is_sequential_array = true;
      }

      $serialized = "";

      if ( $is_sequential_array ) { $serialized .= '['; }
      else { $serialized .= "{\n"; }

      foreach ( $element as $key => $value ) {

        if ( !$is_sequential_array ) {
          for ( $i=0; $i < $functionIterationCounter; $i++ ) {
            $serialized .= " ";
          }
          $serialized .= $key . ":";
        }


        if ( is_array($value) ) {
          $functionIterationCounter++;
          $serialized .= zerializeFunction( $value, true ); //Array inside the array, I am going to use recursivity
        } else {
          if ( is_numeric ( strpos ( $value, ':/' ) ) ) {  //To escape the string (avoid the close of the key [':/'])
            $value = addslashes ( $value );
            $valueExplodeCount = substr_count ( $value, ':/' );
            $valueExplode = explode ( ':/', $value );

            $value = '';
            $i = 0;
            for ( ; $i < $valueExplodeCount; $i++ ) {
              $value .= $valueExplode[ $i ] . ':/\\';
            }
            $value .= $valueExplode[$i];
          }

          $serialized .= $value;
        }


        if ( $is_sequential_array ) { 
          $serialized .= ";";
        }
        else { $serialized .= ":/" . $key . ",\n"; }
      }

      $functionIterationCounter--;
      if ( $is_sequential_array ) { 
        $serialized .= ']'; $serialized = str_replace ( ';]', ']', $serialized ); 

      } else {
        for ( $i=0; $i < $functionIterationCounter; $i++ ) {
          $serialized .= " ";
        }
        $serialized .= "}";
      }
    
    } else {
      $serialized = $element;
    }

    return ( $serialized );
  }


  function zerialize ( $element ){  //To avoid the programmer send as the second parameter "true" (the parameter of recursivity)
    return zerializeFunction ( $element );
  }


                                                                          //DECODE------
                                           //TO OBTAIN HASH
  function getKey( $string, $sizeString, $pos ) {
    $i = $pos;
    $key = '';
    $char = '';
    $keyEnding = ':';

    while ( $i < $sizeString ) {
      list( $char, $i ) = getNextValidChar( $string, $sizeString, $i );

      if ( $char !== $keyEnding ) {
        $key .= $char;
      } else { break; }
    }

    return ( [ $key, $i ] );
  }

  function getValueUntilKey ( $string, $sizeString, $pos, $key ) {
    $keyClose = ':/' . $key . ',';
    $keyCloseSize = strlen( $keyClose );

    $value = '';

    for ( $i = $pos; $i < $sizeString; $i++ ) {
      $searchingKeyClose = substr ( $string, $i, $keyCloseSize );

      if ( $searchingKeyClose !== $keyClose ) {
        $value .= $string[$i]; //Assign the value char by char while $searchingKeyClose is different to $keyClose
      } else { break; }
    }

    $i += ( $keyCloseSize + 1 );

    return ( [ $value, $i ] );
  }

  function getHashDataUntilClose( $string, $sizeString, $pos ) {
    $hashClose = '}';
    $hashData = array();
    $pos++;

    do {
      $pos--; //We subtract because the getKey function calls the getNextValidChar function, which increases the pos +1

      list( $key, $pos ) = getKey ( $string, $sizeString, $pos );

      list( $value, $pos ) = getValueUntilKey ( $string, $sizeString, $pos, $key );

      list( $hashData[ $key ], $i ) = validateString( $value );

      list( $char, $pos ) = getNextValidChar( $string, $sizeString, $pos );
    } while ( ( $char !== $hashClose ) && ( $pos < $sizeString ) );

    return( [ $hashData, $pos ] );
  }

                                           //TO OBTAIN ARRAY

  function getValueUntilCommaOrClose( $string, $sizeString, $pos, $arrayClose ) {
    $valueClose = ';';
    list( $value, $i ) = validateString( $string, $pos );
    $closeArray = false;

    if ( is_string( $value ) ) {
      $value = '';

      for ( $i = $pos; $i < $sizeString; $i++ ) {
        $char = $string[ $i ];

        if ( $char === $arrayClose ) {
          $closeArray = true;
          break;
        } else if ( $char !== $valueClose ) {
          $value .= $char; //Assign the value char by char while $char is different to $valueClose
        } else { break; }
      }
    }

    $i += 1;

    return ( [ $value, $closeArray, $i ] );
  }


  function getArrayDataUntilClose( $string, $sizeString, $pos ) {
    $arrayClose = ']';
    $arrayData = array();
    $arrayPos = 0;

    do {
      list( $value, $closeArray, $pos ) = getValueUntilCommaOrClose( $string, $sizeString, $pos, $arrayClose );

      $arrayData[ $arrayPos ] = $value;
      $arrayPos++;
    } while ( ( !$closeArray ) && ( $pos < $sizeString ) );

    return( [ $arrayData, $pos ] );
  }

                                      //CONTROL CENTER FUNCTION
  function validateString( $string, $pos = 0 ) {
    $hashOpen = '{';
    $arrayOpen = '[';
    $data = null;

    $sizeString = strlen( $string );

    list( $char, $i ) = getNextValidChar( $string, $sizeString, $pos );

    if ( $char === $hashOpen ) {
      list( $data, $i ) = getHashDataUntilClose( $string, $sizeString, $i );
    } else if ( $char === $arrayOpen ) {
      list( $data, $i ) = getArrayDataUntilClose( $string, $sizeString, $i );
    } else {

      $data = stripslashes ( $string );

      if ( is_numeric( strpos ( $string, ':/\\' ) ) ) {  //To remove escape
        $dataExplodeCount = substr_count ( $data, ':/\\' );
        $dataExplode = explode ( ':/\\', $data );

        $data = '';
        $i = 0;
        for ( ; $i < $dataExplodeCount; $i++ ) {
          $data .= $dataExplode[ $i ] . ':/';
        }
        $data .= $dataExplode[ $i ];
      }
    }

    return( [ $data, $i ] );
  }

  function parse_serialize( $string ) {
    list( $data, $i ) = validateString( $string );
    
    return( $data );
  }
