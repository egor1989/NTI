<?
$test="{\"method\":\"addNTIFile\",\"params\":{\"ntifile\":[

  {

    \"type\" : \"open\",

    \"gps\" : {

      \"distance\" : \"0.00\",

      \"longitude\" : \"0.000000\",

      \"direction\" : \"0.0\",

      \"latitude\" : \"0.000000\",

      \"compass\" : \"0\",

      \"speed\" : \"0.0\"

    },

    \"timestamp\" : \"1337177394\",

    \"acc\" : {

      \"x\" : \"0.000000\",

      \"y\" : \"0.000000\"

    }

  },

  {

    \"type\" : \"open\",

    \"gps\" : {

      \"distance\" : \"0.00\",

      \"longitude\" : \"0.000000\",

      \"direction\" : \"0.0\",

      \"latitude\" : \"0.000000\",

      \"compass\" : \"0\",

      \"speed\" : \"0.0\"

    },

    \"timestamp\" : \"1337177420\",

    \"acc\" : {

      \"x\" : \"0.000000\",

      \"y\" : \"0.000000\"

    }

  },

  {

    \"type\" : \"open\",

    \"gps\" : {

      \"distance\" : \"0.00\",

      \"longitude\" : \"0.000000\",

      \"direction\" : \"0.0\",

      \"latitude\" : \"0.000000\",

      \"compass\" : \"0\",

      \"speed\" : \"0.0\"

    },

    \"timestamp\" : \"1337177645\",

    \"acc\" : {

      \"x\" : \"0.000000\",

      \"y\" : \"0.000000\"

    }

  },

  {

    \"type\" : \"open\",

    \"gps\" : {

      \"distance\" : \"0.00\",

      \"longitude\" : \"0.000000\",

      \"direction\" : \"0.0\",

      \"latitude\" : \"0.000000\",

      \"compass\" : \"0\",

      \"speed\" : \"0.0\"

    },

    \"timestamp\" : \"1337178296\",

    \"acc\" : {

      \"x\" : \"0.000000\",

      \"y\" : \"0.000000\"

    }

  }

]}}";
$compressed   = gzencode($test);
echo $compressed ;
echo "<br/><br/>";
$uncompressed = gzdecodes($compressed);
echo $uncompressed ;
echo "<br/><br/>";
$data="1f8b0800 00000000 0003e590 4d0ac230 1085f73d 45987591 d4168a3d 80e0c695 3b572119 6ba0f9a1 896029de ddb469ad 5da80730 9045bef7 266f667a 50e8af46 40054c88 e3e9b097 0d420a96 b54c39a8 7ad05e5e 06569d13 42fa7009 01df5904 52113016 35a411d6 d60d2c5a c25b48e7 99e6d148 37944ec6 203546d7 d2dfc4a2 0d67d185 6c917b69 f4acbf95 32ffad92 1b659973 515db0b3 88e2f5d9 481f53db 5e2a0c8d 2a3bca59 9e975959 e6bb621e 8b71be1a ebfe21b9 5bf39891 4c39ffb2 b7624b7f eded090e 640f4d72 020000>";
	$data=str_replace("<","",$data);
	$data=str_replace(">","",$data);
	$data=str_replace(" ","",$data);
	//$rrrr=gzdecode(pack('H*',strtoupper($data)));
	$some=pack('H*',strtoupper($data));
$uncompressed = gzdecodes($some);
echo $uncompressed ;
echo "<br/><br/>";

echo pack("H*",strtoupper($data));
echo "<br/><br/><br/>";
$compressed   = gzencode("hello world");
echo $compressed;
echo "<br/><br/><br/>";
$uncompressed = gzdecode($compressed);
echo $uncompressed;
phpinfo();
function gzdecodes($data) 
{ 
   return gzinflate(substr($data,10,-8)); 
} 
?>

