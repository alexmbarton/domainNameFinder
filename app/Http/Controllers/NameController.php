<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NameController extends Controller
{
    public function show(Request $request,$word)
    {

        $words = json_decode($this->fetchWord($word));

        $options_output = collect();

        foreach ($words->noun->syn as $word)
        {
            $domains = json_decode($this->fetchDomains($word)); 

            foreach ($domains->results as $domain) 
            {
                if( $domain->availability == "available")
                {
                    $options_output->push($domain);
                }
            }
        }
        $output = $options_output->pluck("domain");

        return response()->json([
                "success"=>[
                    "message"=>"Domains Found!",
                    "data" => $output,
                    "status_code" => 200
                ]
            ]);
        return $options_output;  
    }

    public function fetchWord($word)
    {
        $curl = curl_init();

        $url = "http://words.bighugelabs.com/api/2/9aaea29a82de7e2fce8888d57f20bc35/{$word}/json";

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);
        
        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function fetchDomains($word)
    {
        $curl = curl_init();

        $url = "https://domainr.p.mashape.com/v1/search?mashape-key=GPNMAE3i51mshdvYznEIaLUKr9PHp1CWMo8jsnrFzw4p7BVQKN&q={$word}";

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);
        
        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
