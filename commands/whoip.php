<?php 
/**
* Gets information about a PUBLIC IP from telize.com
* Autor: Armando Uch. jahrmando[at]gmail.com
*/
class whoip extends command
{
    function __construct()
    {
        $this->name = 'whoip';
        $this->public = true;
    }

    public function help()
    {
        return 'Uso: !whoip <A valid IPv4 address> . Gets information about a PUBLIC IP from telize.com';
    }

    protected function Request($args)
    {
        $url = "http://www.telize.com/geoip/" . $args;
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1
        ));
        $body = curl_exec($ch);
        curl_close($ch);
        return json_decode($body);
    }

    public function process($args)
    {
        $pattern = "/^(\d{1,3}\.){3}\d{1,3}$/";
        if (preg_match($pattern, $args)) {
            $response = $this->Request($args);
            if ($response->ip) {
                $this->output .= $response->country.' '.$response->city.' '.$response->region. "\n";
                $this->output .= $response->isp. "\n";
                $this->output .= 'Mapa: '.$response->latitude.', '.$response->longitude. "\n";
            } else { $this->output .= $response->message; }
        } else { $this->output = 'Bad IPv4! :('; }
    }
}
?>