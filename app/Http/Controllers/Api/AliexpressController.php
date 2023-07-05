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
        $productId = $id;
        $url = "https://pt.aliexpress.com/item/$productId.html";       

        include(app_path().'/Services/Aliexpress/IopSdk.php');

        $c = new \IopClient('https://api-sg.aliexpress.com/sync', $_ENV['ALI_APPKEY'], $_ENV['ALI_SECRET']);
        $request = new \IopRequest('aliexpress.affiliate.link.generate');
        $request->addApiParam('app_signature', 'asdasdas');
        $request->addApiParam('promotion_link_type', '0');
        $request->addApiParam('source_values', $url);
        $request->addApiParam('tracking_id', $_ENV['ALI_TRACKID']);
        $response = json_decode($c->execute($request));
        $response_result = $response->aliexpress_affiliate_link_generate_response->resp_result->result->promotion_links->promotion_link;
        
        if (isset($response_result[0]->promotion_link)) {
            $newLink = $response_result[0]->promotion_link;
            $productInfo = $this->getProductInfo($productId)->original;
    
            return response()->json([
                'original_id' => $productId,
                'link' => $newLink,
                'title' => $productInfo['title'],
                'image' => $productInfo['image'],
                'price' => $productInfo['price'],
                'discount' => $productInfo['discount'],
                'category1' => $productInfo['category1'],
                'category2' => $productInfo['category2']
            ]);
        } else {
            return response()->json([
                'erro'=>'Esse produto não tem suporte a link de afiliado',
            ]);
        }
    }

    public function getProductInfo($id)
    {
        //$id = $request->id;
        
        //include(app_path().'/Services/Aliexpress/IopSdk.php');
        $productId = $id;
        $c = new \IopClient('https://api-sg.aliexpress.com/sync', $_ENV['ALI_APPKEY'], $_ENV['ALI_SECRET']);
        $request = new \IopRequest('aliexpress.affiliate.productdetail.get');
        $request->addApiParam('app_signature','aaaaa');
        $request->addApiParam('fields','commission_rate,sale_price');
        $request->addApiParam('product_ids', $productId);
        $request->addApiParam('target_currency','BRL');
        $request->addApiParam('target_language','PT');
        $request->addApiParam('tracking_id','hardlevel');
        $request->addApiParam('country','BR');
        //var_dump($c->execute($request));
        $response =  json_decode($c->execute($request));
        $data = $response->aliexpress_affiliate_productdetail_get_response->resp_result->result->products->product[0];

        return response()->json([
            'title' => $data->product_title,
            'image' => $data->product_main_image_url,
            'discount' => $data->discount,
            'price' => $data->target_sale_price,
            'category1' => $data->first_level_category_name,
            'category2' => $data->second_level_category_name
        ]);
        //return response()->json(['teste' => $id]);
    }

    public function teste()
    {
        return response()->json(['ping' => 'pong']);
    }
}
