<?php 
/**
* Is website down for everyone or just me? (Only HTTP)
* Autor: Armando Uch. jahrmando[at]gmail.com
*/
class isdown extends command
{
    function __construct()
    {
        $this->name = 'isdown';
        $this->public = true;
    }

    public function help()
    {
        return 'Uso: $isdown <Valid Domain> . Verifica el estado de un sitio web (Solo HTTP)';
    }

    protected function Request($args)
    {
        $url = "http://www.isup.me/" . $args;
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1
        ));
        $body = curl_exec($ch);
        curl_close($ch);
        return $body;
    }

    public function process($args)
    {
        $pattern = "/^((?!-)[a-zA-Z0-9\-]*\b\.){1,2}([a-zA-Z]{2,6})(\.[a-zA-Z]{2,6})?$/";
        if (preg_match($pattern, $args)) {
            $chain = $this->Request($args);
            preg_match("/(It's (not )?just you(\.|!))/", $chain, $result);
            switch ($result[0]) {
                case "It's just you.":
                    $this->output = $result[0] ." ".$args." is up";
                    break;
                case "It's not just you!":
                    $this->output = $result[0] ." ".$args." is down";
                    break;
                default:
                    $this->output = ':( Bad request from API.';
                    break;
            }
        } else { $this->output = 'Bad URL! :('; }
    }
}
?>