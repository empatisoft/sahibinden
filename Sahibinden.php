<?php

class Sahibinden {

    private $baseUrl = 'https://www.sahibinden.com/';
    private $browser = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36';

    public function parser($url, $level = 1) {

        $headers = array(
            'Host' => 'www.sahibinden.com',
            'User-Agent' => $this->browser,
            'Sec-Fetch-User' => '?1',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Sec-Fetch-Site' => 'none',
            'Sec-Fetch-Mode' => 'navigate',
            'Cookie' => 'st=a16cdef386e4147c3bd008491c5fd16a13323a4039031e1e42dcf402f6a3b62859bbd6a85808ff082e61b497b5911ff475af01d471874db6b; vid=805; cdid=BmX1TLSPFpk9FOjT5dde88c2; __gfp_64b=QkvbBTpJnTzNPKdBxWiIBG0RBrt29EzTkscpnKWp1LX.s7; _fbp=fb.1.1574865092009.1636609448; h28s1ZLRQ2=A545Rq1uAQAAncXY2EU6HCQ1LomMVZFQ8S0n7jNopXUPe7Y92IlX8x8HPcMmAbkL-XKuco24wH8AAOfvAAAAAA==; _ga=GA1.2.345871416.1574865095; nwsh=std; showPremiumBanner=false; MS1=; segIds=; 360HomeSplashClosed=true; _gid=GA1.2.490441197.1575293221; geoipCity="";'
        );

        $options = array(
            CURLOPT_TIMEOUT => 0,
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => true,     //return headers in addition to content
            CURLOPT_FOLLOWLOCATION => false,     // follow redirects
            CURLOPT_ENCODING       => "utf-8",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST           => 0,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_USERAGENT => $this->browser
        );
        $ch      = curl_init($this->baseUrl.$url);
        curl_setopt_array( $ch, $options );
        $rough_content = curl_exec( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
        $header_content = substr($rough_content, 0, $header['header_size']);
        $body = trim(str_replace($header_content, '', $rough_content));

        $categories = array();
        $keys = array();
        $values = array();

        $d = new DOMDocument;
        $d->loadHTML(mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8'));

        $x = new DOMXPath($d);

        foreach ($x->query('//li[contains(@class, "cl'.$level.'")]') as $el) {

            $removed = trim(preg_replace('/\s+/', ' ', $d->saveHTML($el)));
            preg_match('/<li class="cl'.$level.'"> <a href="\/(.*)">(.*)<\/a> <span>((.*))<\/span> <\/li>/', $removed, $category);
            array_push($categories, array($category[1] => $category[2]));
        }

        if(!empty($categories)) {
            foreach ($categories as $key => $value) {

                foreach ($value as $k => $v) {
                    array_push($keys, $k);
                    array_push($values, $v);
                }
            }
        }

        $result = array_combine($keys, $values);

        return $result;

    }


}
