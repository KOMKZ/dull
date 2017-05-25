<?php
namespace wxpay\helpers;

/**
 *
 */
class XmlHelper
{
    public static function arrayToXml($array)
    {
        if(!is_array($array)
            || count($array) <= 0)
        {
            throw new \Exception("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($array as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    public static function xmlToArray($xml){
        if(!$xml){
            throw new \Exception("xml数据异常！");
        }
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}
