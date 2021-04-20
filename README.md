# Zerializer


###### Serializer and deserializer made in **PHP**

There are some examples of the codification:

### 1. HASH
* **Send:**
```
Array
(
    [key0] => 123
    [key1] => 'a:/b:/c:/d:/efgh'
    [key2] => Array
        (
            [0] => 1
            [1] => 2
            [2] => Array
                (
                    [key2_0] => 3
                    [key2_1] => 4
                )

            [3] => 5
            [4] => Array
                (
                    [key2_2] => 4
                    [0] => Array
                        (
                            [key2_2_0] => 4
                            [key2_2_1] => 5
                        )

                    [key2_3] => 5
                )

        )

    [key3] => Array
        (
            [0] => 7
            [1] => 8
            [2] => Hello\'World'
        )

    [key4] => Array 
        (
            [key4_0] => 9
            [key4_1] => Hello :/a \a \ World
        )
)
```

* **Serialized:**
    
```
{
 key0:123:/key0,
 key1:\'a:/\b:/\c:/\d:/\efgh\':/key1,
 key2:[1;2;{
   key2_0:3:/key2_0,
   key2_1:4:/key2_1,
  };5;{
   key2_2:4:/key2_2,
   0:{
    key2_2_0:4:/key2_2_0,
    key2_2_1:5:/key2_2_1,
   }:/0,
   key2_3:5:/key2_3,
  }]:/key2,
 key3:[7;8;Hello\'World']:/key3,
 key4:{
  key4_0:9:/key4_0,
  key4_1:Hello :/\a \\a \\ World:/key4_1,
 }:/key4,
}
```

* **Deserialized:** Returns the same you sent.



### 2. ORDERED ARRAY
* **Send:**
```
Array
(
    [0] => 0
    [1] => 1
    [2] => Array
        (
            [0] => 2
            [1] => Array
                (
                    [0] => 3
                    [1] => Array
                        (
                            [0] => 4
                            [1] => 5
                        )

                    [2] => 6
                    [3] => 7
                )

        )

    [3] => 8
    [4] => Array
        (
            [0] => 9
            [1] => 10
        )

    [5] => Array
        (
            [0] => 11
            [1] => 12
        )

    [6] => 13
)
```

* **Serialized:**
    
```
[0;1;[2;[3;[4;5];6;7]];8;[9;10];[11;12];13]
```
* **Deserialized:** Returns the same you sent.
