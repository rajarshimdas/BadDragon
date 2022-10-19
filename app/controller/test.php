helloworld!
<?php

test($m);


// Function with controller name will be fired
function test($m)
{
    if ($m){
        echo 'm: '.$m;
    }
    
    /* Check if its a valid method 
    if (function_exists($m)) {
        // Fly
        echo 'ok';
    } else {
        // Die
        echo 'no-method';
    }
    */
}

function hello_world()
{
}
