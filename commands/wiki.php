<?php
class wiki extends command {

    public function __construct()
    {
		$this->name = 'wiki';
		$this->public = true;
	}

    public function help()
    {
		return "Uso: !wiki <tema> - Muestra 3 extractos de artículos (Wikipedia) referentes a <tema>.";
	}

    public function process($args)
    {
		$args = trim($args);

		if(''===$args) {
			$output = "El comando {$this->name} necesita un tema o palabra a buscar.";
		} else {
            $query = urlencode($args);
            $api   = "https://es.wikipedia.org/w/api.php?"
                        ."action=query&"
                        ."list=search&"
                        ."format=php&"
                        ."srwhat=text&"
                        ."srsearch={$query}&"
                        ."srlimit=3&"
                        ."srprop=snippet&";

            $res = unserialize(file_get_contents($api));

            if ($res['query']['search']) {
                foreach ($res['query']['search'] as $r) {
                    $uri_title = str_replace(" ", '_', $r['title']);
                    $output[]  = $r['title'] . ' - ' . strip_tags($r['snippet']) . "(http://es.wikipedia.org/wiki/{$uri_title})";
                }
                $output = join("\n", $output);
            } else {
                $output = "No se encontraron resultados para {$args}.";
            }
		}
        $this->output = $output;
	}

}
