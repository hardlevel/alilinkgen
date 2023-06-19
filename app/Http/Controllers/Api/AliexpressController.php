<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Aliexpress\IopClient;

class AliexpressController extends Controller
{
    public function index(Request $request, $url, $type)
    {
        $url = $request->url;
        $type = $request->type;
        if ($type == 1){
            $newUrl = 'https://pt.aliexpress.com/item/'. $url .'.html';
        } elseif ($type == 2) {
            $newUrl = 'https://s.click.aliexpress.com/e/'. $url;
        }

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
        //$request->addApiParam('source_values', 'https://pt.aliexpress.com/item/1005004698856770.html?spm=a2g0o.productlist.main.1.35ec35cc810UVF&algo_pvid=c81f3d4c-84c5-4697-b724-5e1119a85416&algo_exp_id=c81f3d4c-84c5-4697-b724-5e1119a85416-0&pdp_npi=3%40dis%21BRL%211551.56%21209.32%21%21%21%21%21%40211beca116869232656221380d07ea%2112000033874163478%21sea%21BR%21160017240&curPageLogUid=fFtV0W5AFknu');
        $request->addApiParam('source_values', $newUrl);
        $request->addApiParam('tracking_id', $_ENV['ALI_TRACKID']);
        $response = json_decode($c->execute($request));
        $newLink = $response->aliexpress_affiliate_link_generate_response->resp_result->result->promotion_links->promotion_link;
        //teste
        return response()->json(['link' => $newLink]);
        //return response()->json(['link' => $url . ' - ' . $type]);
    }
}
