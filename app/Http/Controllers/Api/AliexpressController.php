<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Aliexpress\IopClient;
use DOMDocument;

class AliexpressController extends Controller
{
    public function index(Request $request, $id)
    {
        $id = $request->id;
        $url = "https://pt.aliexpress.com/item/$id.html";
        // $type = $request->type;
        // if ($type == 1){
        //     $newUrl = 'https://pt.aliexpress.com/item/'. $url .'.html';
        // } elseif ($type == 2) {
        //     $newUrl = 'https://s.click.aliexpress.com/e/'. $url;
        // }

        //$data = $this->getProductInfo($newUrl);
        //$data = $this->getProductInfo($url);

        // include(app_path().'/Services/Aliexpress/iop/IopClient.php');
        // include(app_path().'/Services/Aliexpress/iop/IopRequest.php');
        // include(app_path().'/Services/Aliexpress/iop/Constants.php');
        // include(app_path().'/Services/Aliexpress/iop/IopLogger.php');
        // include(app_path().'/Services/Aliexpress/iop/UrlConstants.php');

        include(app_path().'/Services/Aliexpress/IopSdk.php');

        $c = new \IopClient('https://api-sg.aliexpress.com/sync', $_ENV['ALI_APPKEY'], $_ENV['ALI_SECRET']);
        $request = new \IopRequest('aliexpress.affiliate.link.generate');
        $request->addApiParam('app_signature', 'asdasdas');
        $request->addApiParam('promotion_link_type', '0');
        $request->addApiParam('source_values', $url);
        $request->addApiParam('tracking_id', $_ENV['ALI_TRACKID']);
        $response = json_decode($c->execute($request));
        $newLink = $response->aliexpress_affiliate_link_generate_response->resp_result->result->promotion_links->promotion_link;
        
        // return response()->json([
        //     'link' => $newLink,
        //     'title' => $data['title'],
        //     'image' => $data['image']
        // ]);

        //return response()->json(['link' => $id . ' - ' . $c->execute($request)]);
        //return response()->json(['link' => $title]);
        return response()->json(['link' => $newLink]);
    }

    public function getProductInfo($url)
    {
        //$url2 = "https://pt.aliexpress.com/item/$url.html";
        $maxAttempts = 5; // Número máximo de tentativas
        $attempt = 1;
        $html = '';
        $title = '';
        $ogImage = '';

        while ($attempt <= $maxAttempts && empty($title)) {
            // Inicializa a sessão cURL
            $curl = curl_init($url);

            // Configura as opções do cURL
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // Executa a solicitação HTTP
            $html = curl_exec($curl);

            // Verifica se a solicitação foi bem-sucedida
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                // Imprime o conteúdo HTML para depuração
                //echo $html;

                // Analisa o HTML usando a biblioteca DOMDocument
                $dom = new DOMDocument();
                @$dom->loadHTML($html);

                // Obtém o título da página a partir da tag <h1> ou <h2>
                $titleTag = $dom->getElementsByTagName('title');
                if ($titleTag->length > 0) {
                    $title = $titleTag->item(0)->nodeValue;
                }

                $metaTags = $dom->getElementsByTagName('meta');
                foreach ($metaTags as $metaTag) {
                    if ($metaTag->getAttribute('property') === 'og:image') {
                        $ogImage = $metaTag->getAttribute('content');
                        break;
                    }
                }
            }

            // Fecha a sessão cURL
            curl_close($curl);

            $attempt++;
        }

        // Exibe o título e a imagem para depuração
        // echo 'Título: ' . $title . '<br>';
        // echo 'Imagem: ' . $ogImage . '<br>';

        // $data = [
        //     'title' => $title,
        //     'image' => $ogImage
        // ];

        //return $data;
        return response()->json(['title' => $title,'image' => $ogImage]);
    }

    public function teste()
    {
        return response()->json(['ping' => 'pong']);
    }
}
