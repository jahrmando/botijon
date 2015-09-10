<?php 
/**
* Comando para consultar informacion de alguna CVE
*/
class cve extends command
{
    
    function __construct()
    {
        $this->name = 'cve';
        $this->public = true;
    }

    public function help()
    {
        $chain = 'Uso: Despliega informacion de incidentes de seguridad (CVE)' . "\n";
        $chain .= '$cve 0000-00000 d1 d2 dN.. . Busqueda por clave CVE, (4 max.)' . "\n";
        $chain .= '$cve last . Despliega los ultimos 12 CVEs reportados' . "\n";
        $chain .= '$cve browse <vendor> . Despliega productos del vendedor (Limitado)' . "\n";
        $chain .= '$cve browse <vendor> <product> . Busca incidentes del producto (10 max.)' . "\n";
        return $chain;
    }

    protected function JsonRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $body = curl_exec($ch);
        curl_close($ch);
        return json_decode($body);
    }

    protected function Search($args)
    {
        $url = "http://cve.circl.lu/api/cve/CVE-" . $args;
        return $this->JsonRequest($url);
    }

    protected function LastCVE()
    {
        $url = "http://cve.circl.lu/api/last";
        return $this->JsonRequest($url);
    }

    protected function BrowseProducts($args)
    {
        $url = "http://cve.circl.lu/api/browse/" . $args;
        return $this->JsonRequest($url);
    }

    protected function SearchProduct($vendor, $product)
    {
        $url = "http://cve.circl.lu/api/search/$vendor/$product";
        return $this->JsonRequest($url);
    }

    public function process($args, $maxSearch=4)
    {
        $this->output = '';
        $argsList = explode(' ', preg_replace('/\s{2,}/', ' ', trim($args)));

        switch ($argsList[0]) {
            case 'last':
                $lastList = $this->LastCVE();
                $count = 0;
                foreach ($lastList->results as $cve) {
                    $count++;
                    $this->output .= $cve->id."\n";
                    if ($count >= 12) { break; }
                }
                break;

            case 'browse':
                $count = 0;
                if (count($argsList) > 2) {
                    $listCVE = $this->SearchProduct($argsList[1], $argsList[2]);
                    foreach ($listCVE as $cve) {
                        $this->output .= $cve->id . "\n";
                        $count++;
                        if ($count >= 10) { break; }
                    }
                } else {
                    $listProducts = $this->BrowseProducts($argsList[1]);
                    $products = '';
                    $max = 0;
                    if (!empty($listProducts->product)) {
                        foreach ($listProducts->product as $product) {
                            $products .= $product . ' ';
                            $count++;
                            if ($count >= 12) {
                                $this->output .= $products . "\n";
                                $products = '';
                                $count = 0;
                                $max++;
                            } elseif ($max >= 10) { break; }                    
                        }
                        $this->output .= $products;
                    }
                }
                break;

            case '':
                $this->output = $this->help();
                break;

            default:
                $count = 0;
                foreach ($argsList as $value) {
                    $result = $this->Search(preg_replace('/CVE-/', '', $value));
                    $count++;
                    if ( empty($result->id) ) {
                        $this->output .= "No hay resultados para este CVE" . "\n";
                    } else {
                        $this->output .= "CVE: ". $result->id . "\n";
                        $this->output .= "Published: ". $result->Published . "\n";
                        $this->output .= "Vector: ". $result->access->vector . "\n";
                        $this->output .= "Complexity: ". $result->access->complexity . "\n";
                        $this->output .= "Summary: ". $result->summary . "\n";
                    }
                    if ($count == $maxSearch) { break; }
                }
                break;
        }
    }
}
?>